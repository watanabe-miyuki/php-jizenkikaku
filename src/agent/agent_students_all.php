<?php
require($_SERVER['DOCUMENT_ROOT'] . "/db_connect.php");
session_start();

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"]) || !isset($_SESSION['corporate_name'])) {
    header("Location: agent_login.php");
    exit();
}

// 全てのエージェントの掲載ステータスをupdateする。
date_default_timezone_set('Asia/Tokyo');
$today = date("Y-m-d");
// 掲載再開
$stmt = $db->prepare('update agents set list_status=1 where started_at <= :started_at and ended_at >= :ended_at');
$stmt->bindValue(':started_at', $today, PDO::PARAM_STR);
$stmt->bindValue(':ended_at', $today, PDO::PARAM_STR);
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}

// 申し込み上限数到達(今月の申し込み数と比較)
// 全てのエージェントでforeach
// 全てのエージェント
$stmt = $db->query('select id from agents');
$stmt->execute();
$agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 今月の申し込み数
foreach ($agents as $agent) {
    $stmt = $db->prepare('SELECT * FROM students AS S, students_contacts AS SC, agents AS A WHERE S.id = SC.student_id AND SC.agent_id = A.id AND SC.agent_id = :agent_id AND DATE_FORMAT(S.created, "%Y-%m") = :form_month ');
    $stmt->bindValue(':form_month', Date('Y-m'), PDO::PARAM_STR);
    $stmt->bindValue(':agent_id', $agent['id'], PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cnt = count($result);
    // 比較
    $stmt = $db->prepare('update agents set list_status=3 where id= :id and application_max <= :application');
    $stmt->bindValue(':id', $agent['id'], PDO::PARAM_INT);
    $stmt->bindValue(':application', $cnt, PDO::PARAM_INT);
    $success = $stmt->execute();
    if (!$success) {
        die($db->error);
    }

    // タグ不足
    $stmt = $db->prepare('select tag_id from agents_tags where agent_id=:id');
    $stmt->bindValue(':id', (int)$agent['id'], PDO::PARAM_INT);
    $stmt->execute();
    $agent_tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (!$agent_tags) {
        $tag_lack = $agent['id'];
    } else {
        foreach ($agent_tags as $agent_tag) {
            $stmt = $db->prepare('select sort_id from filter_tags where tag_id=:tag_id');
            $stmt->bindValue(':tag_id', $agent_tag, PDO::PARAM_STR);
            $stmt->execute();
            $tags[] = $stmt->fetch(PDO::FETCH_COLUMN);
        }
        //タグ情報
        $stmt = $db->query('select fs.id, sort_name, tag_id, tag_name from filter_sorts fs inner join filter_tags ft on fs.id = ft.sort_id;
');
        $filter_sorts_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($filter_sorts_tags as $f) {
            if (!in_array($f['id'], $tags)) {
                $tag_lack = $agent['id'];
            }
        }
    }
    $stmt = $db->prepare('update agents set list_status=4 where id= :id');
    if (isset($tag_lack)) {
        $stmt->bindValue(':id', $tag_lack, PDO::PARAM_INT);
        $success = $stmt->execute();
        if (!$success) {
            die($db->error);
        }
    }
}

// 掲載期間外
$stmt = $db->prepare('update agents set list_status=2 where started_at > :started_at or ended_at < :ended_at');
$stmt->bindValue(':started_at', $today, PDO::PARAM_STR);
$stmt->bindValue(':ended_at', $today, PDO::PARAM_STR);
$stmt->execute();
$success = $stmt->execute();
if (!$success) {
    die($db->error);
}
// upadateここまで

$id = $_SESSION['id'];
$message = $_SESSION['corporate_name'] . "様ようこそ";
$stmt = $db->prepare('select * from agents where id = :id');
$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
$stmt->execute();
$agent = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['month'] = filter_input(INPUT_POST, 'month', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
} else {
    $form['month'] = Date('Y-m');
}


try {

    if (isset($form["month"])) {
        if (!$form["month"]) {
            $month = "all"; //全てを表示
        } else {
            $month = $form["month"];
        }
        // セレクトボックスで選択された値を受け取る
    } else {
        $month = Date('n');
    }
    // 全ての問い合わせ
    $stmt = $db->prepare('SELECT
S.created AS 問い合わせ日時, 
S.name AS 氏名, 
S.email AS メールアドレス, 
S.tel AS 電話番号, 
S.collage AS 大学,
S.department AS 学科,
S.class_of AS 何年卒,
SC.id AS 問い合わせID,
SC.valid_status_id AS 無効判定
FROM students AS S, students_contacts AS SC WHERE S.id = SC.student_id AND SC.agent_id = :agent_id ORDER BY S.created desc');
    if (!$stmt) {
        die($db->error);
    }
    $stmt->bindValue(':agent_id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $all_contact = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // 重複検査

    foreach ($all_contact as $contact) {
        $stmt = $db->prepare(
            'SELECT SC.id FROM students AS S, students_contacts AS SC WHERE (S.email = :email OR S.name = :name OR S.tel = :tel) AND S.id = SC.student_id AND SC.agent_id = :agent_id ORDER BY S.created desc'
        );
        if (!$stmt) {
            die($db->error);
        }
        $stmt->bindValue(':email', $contact['メールアドレス'], PDO::PARAM_STR);
        $stmt->bindValue(':name', $contact['氏名'], PDO::PARAM_STR);
        $stmt->bindValue(':tel', $contact['電話番号'], PDO::PARAM_STR);
        $stmt->bindValue(':agent_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $duplicate_ids[$contact['問い合わせID']] =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        // 重複id(自分含む)→掲載時判別
    }

    if ($month != "all") :
        $stmt = $db->prepare('SELECT 
    DATE_FORMAT(S.created, "%Y-%m") AS prepare_month,
    DATE_FORMAT(S.created, "%m") AS 月,
	S.created AS 問い合わせ日時, 
	S.name AS 氏名, 
	S.email AS メールアドレス, 
	S.tel AS 電話番号, 
	S.collage AS 大学,
	S.department AS 学科,
	S.class_of AS 何年卒,
    SC.id AS 問い合わせID,
    SC.valid_status_id AS 無効判定
    FROM
    students AS S, students_contacts AS SC, agents AS A
    WHERE 
    S.id = SC.student_id
    AND
    SC.agent_id = A.id
    AND
    -- A.id = :agent_id
    SC.agent_id = :agent_id
    AND
    DATE_FORMAT(S.created, "%Y-%m") = :form_month
    ORDER BY 問い合わせ日時 desc
    ');

        $stmt->bindValue(':form_month', $form['month'], PDO::PARAM_STR);
        $stmt->bindValue(':agent_id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cnt = count($result);
        // var_dump($result);


        // 指定monthの無効students＆請求金額
        $stmt = $db->prepare('SELECT *
FROM
students AS S, students_contacts AS SC, agents AS A
WHERE 
S.id = SC.student_id
AND
SC.agent_id = A.id
AND
SC.agent_id = :agent_id
AND
SC.valid_status_id = :invalid_status_id
AND
DATE_FORMAT(S.created, "%Y-%m") = :form_month
');
        $stmt->bindValue(':form_month', $form['month'], PDO::PARAM_STR);
        $stmt->bindValue(':agent_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':invalid_status_id', (int)3, PDO::PARAM_INT);
        $stmt->execute();
        $invalid = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $invalid_cnt = count($invalid); //無効件数
        $charge_cnt = ($cnt - $invalid_cnt) * $agent['charge']; //請求金額

    else : //全表示
        $result = $all_contact;
        $cnt = count($result);
        // 全てのの無効students＆請求金額
        $stmt = $db->prepare('SELECT *
                FROM
                students AS S, students_contacts AS SC, agents AS A
                WHERE 
                S.id = SC.student_id
                AND
                SC.agent_id = A.id
                AND
                SC.agent_id = :agent_id
                AND
                SC.valid_status_id = :invalid_status_id');
        $stmt->bindValue(':agent_id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':invalid_status_id', (int)3, PDO::PARAM_INT);
        $stmt->execute();
        $invalid = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $invalid_cnt = count($invalid); //請求件数
        $charge_cnt = ($cnt - $invalid_cnt) * $agent['charge']; //請求金
    endif;
} catch (PDOException $e) {
    print('Error:' . $e->getMessage());
    die();
}

if (empty($id)) {
    exit('IDが不正です。');
}
// 無効化申請中/無効化承認済みをタイトルに表示
function set_valid_status($valid_status)
{
    if ($valid_status === 1) {
        return '';
    } elseif ($valid_status === 2) {
        return '申請中';
    } elseif ($valid_status === 3) {
        return '承認済み';
    } elseif ($valid_status === 4) {
        return '申請拒否';
    } else {
        return 'エラー';
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学生情報一覧</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="table.css">
    <link rel="stylesheet" href="agent_students_all.css">
</head>

<body>

    <header>
        <h1>
            <p><span>CRAFT</span>by boozer</p>
        </h1>
        <p class="welcome_agent">ようこそ　<?php echo ($_SESSION['corporate_name']); ?>様</p>
        <nav class="nav">
            <ul>
                <li><a href="agent_students_all.php">学生情報一覧</a></li>
                <li><a href="agent_information.php">登録情報</a></li>
                <li><a href="../index.php">ユーザー画面へ</a></li>
                <li><a href="agent_logoutPage.php">ログアウト</a></li>
            </ul>
        </nav>
    </header>
    <div class="all_wrapper">

        <div class="right_wrapper">
            <h1 class="students_all_title">学生情報一覧
                (<?php echo set_list_status($agent['list_status']); ?>)
            </h1>
            <div class="sum_inquiry_wrapper">
                <p class="sum_inquiry"><span>
                        <?php if ($month != "all") :
                            echo ($form['month']) . '月';
                        else : echo '全て';
                        endif; ?>
                    </span>の問い合わせ件数: <span>
                        <?php echo $cnt ?>
                    </span>件　　　　無効: <span><?php echo $invalid_cnt ?></span> 件
                    　　　　請求金額: <span><?php echo $charge_cnt ?></span> 円</p>
            </div>
            <form action="agent_students_all.php" method="POST">
                <input type="month" name="month" value="<?php echo $form['month']; ?>">
                <input type="submit" name="submit" value="月を変更" />
                <span>※カレンダーの削除ボタンで全てを表示</span>
            </form>
            <?php if ($cnt === 0) : ?>
                <p class="error">ヒットしませんでした。違う月を改めて指定してください</p>
            <?php else : ?>
                <table cellspacing="0">
                    <tr>
                        <th>問い合わせ日時</th>
                        <th>氏名</th>
                        <th>大学</th>
                        <th>学部/学科</th>
                        <th>何年卒</th>
                        <th>ID</th>
                        <th>詳細</th>
                        <th>無効申請</th>
                        <th>重複</th>
                    </tr>
                    <?php foreach ($result as $column) : ?>
                        <tr>
                            <td><?php echo date("Y/m/d H:i:s", strtotime($column['問い合わせ日時'])); ?></td>
                            <td><?php echo h($column['氏名']); ?></td>
                            <td><?php echo h($column['大学']); ?></td>
                            <td><?php echo h($column['学科']); ?></td>
                            <td><?php echo h($column['何年卒']); ?>年度</td>
                            <td><?php echo h($column['問い合わせID']); ?></td>
                            <td><a class="to_students_detail" href="agent_students_detail.php?id=<?php echo h($column['問い合わせID']); ?>">詳細</a>
                            </td>
                            <td><?php echo set_valid_status($column['無効判定']); ?></td>
                            <td>
                                <?php foreach ($duplicate_ids[$column['問い合わせID']] as $d_id) : if ($d_id['id'] !=  $column['問い合わせID']) :
                                        echo 'id' . $d_id['id'] . ' ';
                                    endif;
                                endforeach ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <div class="inquiry">
        <p>お問い合わせは下記の連絡先にお願いいたします。
            <br>craft運営 boozer株式会社事務局
            <br>TEL:080-3434-2435
            <br>Email:craft@boozer.com
        </p>
    </div>
</body>

</html>
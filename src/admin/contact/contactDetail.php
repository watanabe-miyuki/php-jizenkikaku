<?php
session_start();
require('../../db_connect.php');

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
  header("Location: ../login/login.php");
  exit();
}

// 問い合わせid (!=student_id)
$id = $_GET['id'];

$agent_id = $_GET['agent'];

if (empty($id) || empty($agent_id)) {
    exit('IDが不正です。');
}


$stmt = $db->prepare('SELECT * FROM students AS S INNER JOIN students_contacts AS SC ON S.id = SC.student_id where SC.id = :id');
$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
//SQL実行
$stmt->execute();
//結果を取得
$result = $stmt->fetch(PDO::FETCH_ASSOC);
// var_dump($result);

if (!$result) {
    exit('データがありません。');
}


// エージェント取得
$stmt = $db->prepare('SELECT * FROM agents WHERE id=:id');
$stmt->bindValue(':id', (int)$agent_id, PDO::PARAM_INT);
$stmt->execute();
$agent = $stmt->fetch(PDO::FETCH_ASSOC);


// 通報内容
$stmt = $db->prepare('SELECT * FROM invalid_requests  where contact_id = :contact_id');
$stmt->bindValue(':contact_id', (int)$id, PDO::PARAM_INT);
//SQL実行
$stmt->execute();
//結果を取得
$invalid_requests = $stmt->fetch(PDO::FETCH_ASSOC);

// 通報機能
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naiyou = filter_input(INPUT_POST, 'naiyou', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    // 無効化
    if (isset($_POST['invalid'])) {
        // students_contacts.valid_status_idを　3へ
        $stmt = $db->prepare('update students_contacts set valid_status_id=3, reason= :reason where id=:id');
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->bindValue(':reason', $naiyou, PDO::PARAM_INT);
        $stmt->execute();

        //学生問い合わせ確認メール
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        $to = $agent['to_send_email'];
        if(!empty(h($invalid_requests['invalid_request_memo']))){
            $invalid_requests = "お送りいただいた通報内容は以下です。
    ━━━━━━□■□　通報内容　□■□━━━━━━
    " . h($invalid_requests['invalid_request_memo']) . "
    ━━━━━━━━━━━━━━━━━━━━━━━";
        }else{
            $invalid_requests = "";
        }
        
        $subject = '【boozer株式会社】問い合わせ無効承認のお知らせ';
        $message =  "
    ※このメールはシステムからの自動返信です
    
    " . $agent['client_name'] . "様

    お世話になっております。
    boozer株式会社でございます。
    以下の学生を請求対象からお外ししました。
    
    ━━━━━━□■□　学生情報　□■□━━━━━━
    問い合わせID：" . h($id) . "
    氏名：" . h($result["name"]) . "
    申込日時：" . date("Y/m/d H:i:s", strtotime($result['created'])) . "
    電話番号：" . h($result["tel"]) . "
    Email：" . h($result["email"]) . "
    学校名(大学/大学院/専門学校/短大/高校等)：" . h($result["collage"]) . "
    学部/学科：" . h($result["department"]) . "
    卒業年度：" . h($result["class_of"]) . "年卒
    住所：" . h($result["address"]) . "
    備考欄：" . h($result["memo"]) . "
    ━━━━━━━━━━━━━━━━━━━━━━━

    無効承認理由は以下です。
    ━━━━━━□■□　承認理由　□■□━━━━━━
    " . h($naiyou) . "
    ━━━━━━━━━━━━━━━━━━━━━━━

    " . $invalid_requests . "

    また、なにかありましたら、craft@boozer.comにお問い合わせください。なお、営業時間は平日9時〜18時となっております。
    時間外のお問い合わせは翌営業日にご連絡差し上げます。
    
    ご理解・ご了承の程よろしくお願い致します。
    ———————————————————————
    craft運営 boozer株式会社事務局
    担当：山田　太郎
    TEL:080-3434-2435
    Email:craft@boozer.com
    
    【会社情報】
    住所：〒111-1111　東京都港区5-6-7-8
    電話番号：090-1000-2000
    営業時間：平日 9時～18時
    ———————————————————————";
        $header = ['From' => 'craft@boozer.com', 'Content-Type' => 'text/plain; charset=UTF-8', 'Content-Transfer-Encoding' => '8bit'];
        $result = mb_send_mail($to, $subject, $message, $header);
        if (!$result) {
            echo 'メールの送信に失敗しました';
        }
    

    // 無効化拒否
    }elseif (isset($_POST['non_invalid'])) {
        // students_contacts.valid_status_idを　3へ
        $stmt = $db->prepare('update students_contacts set valid_status_id=4, reason= :reason where id=:id');
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->bindValue(':reason', $naiyou, PDO::PARAM_INT);
        $stmt->execute();

        //学生問い合わせ確認メール
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        $to = $agent['to_send_email'];
        $subject = '【boozer株式会社】無効化お断りのお知らせ';
        $message =  "
    ※このメールはシステムからの自動返信です
    
    " . $agent['client_name'] . "様

    お世話になっております。
    boozer株式会社でございます。
    たいへん恐れ入りますが、貴社から頂いた無効化申請をお断りいたしました。理由は以下の通りです。
    ━━━━━━□■□　拒否理由　□■□━━━━━━
    " . h($naiyou) . "
    ━━━━━━━━━━━━━━━━━━━━━━━

    対象の学生は、こちらです。
    
    ━━━━━━□■□　学生情報　□■□━━━━━━
    問い合わせID：" . h($id) . "
    氏名：" . h($result["name"]) . "
    申込日時：" . date("Y/m/d H:i:s", strtotime($result['created'])) . "
    電話番号：" . h($result["tel"]) . "
    Email：" . h($result["email"]) . "
    学校名(大学/大学院/専門学校/短大/高校等)：" . h($result["collage"]) . "
    学部/学科：" . h($result["department"]) . "
    卒業年度：" . h($result["class_of"]) . "年卒
    住所：" . h($result["address"]) . "
    備考欄：" . h($result["memo"]) . "
    ━━━━━━━━━━━━━━━━━━━━━━━

    お送りいただいた通報内容は以下です。
    ━━━━━━□■□　通報内容　□■□━━━━━━
    " . h($invalid_requests['invalid_request_memo']) . "
    ━━━━━━━━━━━━━━━━━━━━━━━


    お断りした理由をご確認いただき、なにかありましたら、craft@boozer.comにお問い合わせください。なお、営業時間は平日9時〜18時となっております。
    時間外のお問い合わせは翌営業日にご連絡差し上げます。
    
    ご理解・ご了承の程よろしくお願い致します。
    ———————————————————————
    craft運営 boozer株式会社事務局
    担当：山田　太郎
    TEL:080-3434-2435
    Email:craft@boozer.com
    
    【会社情報】
    住所：〒111-1111　東京都港区5-6-7-8
    電話番号：090-1000-2000
    営業時間：平日 9時～18時
    ———————————————————————";
        $header = ['From' => 'craft@boozer.com', 'Content-Type' => 'text/plain; charset=UTF-8', 'Content-Transfer-Encoding' => '8bit'];
        $result = mb_send_mail($to, $subject, $message, $header);
        if (!$result) {
            echo 'メールの送信に失敗しました';
        }
    }
        header("location: contactDetail.php?agent=$agent_id&id=$id");
}


// 無効化申請中/無効化承認済みをタイトルに表示
function set_valid_status($valid_status)
{
    if ($valid_status === 1) {
        return '';
    } elseif ($valid_status === 2) {
        return '無効化申請中';
    } elseif ($valid_status === 3) {
        return '無効化承認済み';
    } elseif ($valid_status === 4) {
        return '無効申請拒否';
    } else {
        return 'エラー';
    }
}

// 重複検査
//email重複
$stmt = $db->prepare(
    'SELECT SC.id FROM students AS S, students_contacts AS SC WHERE S.email = :email AND S.id = SC.student_id AND SC.agent_id = :agent_id ORDER BY S.created desc'
);
if (!$stmt) {
    die($db->error);
}
$stmt->bindValue(':email', h($result['email']), PDO::PARAM_STR);

$stmt->bindValue(':agent_id', (int)$agent_id, PDO::PARAM_INT);

$stmt->execute();
$duplicated_emails = $stmt->fetchAll(PDO::FETCH_ASSOC);
//tel重複
$stmt = $db->prepare(
    'SELECT SC.id FROM students AS S, students_contacts AS SC WHERE S.tel = :tel AND S.id = SC.student_id AND SC.agent_id = :agent_id ORDER BY S.created desc'
);
if (!$stmt) {
    die($db->error);
}
$stmt->bindValue(':tel', h($result['tel']), PDO::PARAM_STR);

$stmt->bindValue(':agent_id', (int)$agent_id, PDO::PARAM_INT);

$stmt->execute();
$duplicated_tels = $stmt->fetchAll(PDO::FETCH_ASSOC);
//name重複
$stmt = $db->prepare(
    'SELECT SC.id FROM students AS S, students_contacts AS SC WHERE S.name = :name AND S.id = SC.student_id AND SC.agent_id = :agent_id ORDER BY S.created desc'
);
if (!$stmt) {
    die($db->error);
}
$stmt->bindValue(':name', h($result['name']), PDO::PARAM_STR);

$stmt->bindValue(':agent_id', (int)$agent_id, PDO::PARAM_INT);

$stmt->execute();
$duplicated_names = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>学生情報詳細</title>
</head>
<link rel="stylesheet" href="../css/reset.css" />
<!-- <link rel="stylesheet" href="../css/style.css" /> -->
<link rel="stylesheet" href="../../agent/table.css">
<link rel="stylesheet" href="../../agent/agent_students_detail.css">
<link rel="stylesheet" href="../css/style.css" />



<body>

    <header>
        <div class="header-inner">
            <h1 class="header-title">CRAFT管理者画面</h1>
            <nav class="header-nav">
                <ul class="header-nav-list">
                    <a href="../index.php">
                        <li class="header-nav-item select">エージェント一覧</li>
                    </a>
                    <a href="../add/agentAdd.php">
                        <li class="header-nav-item">エージェント追加</li>
                    </a>
                    <a href="../tags/tagsEdit.php">
                        <li class="header-nav-item">タグ編集</li>
                    </a>
                    <a href="../login/loginInfo.php">
                        <li class="header-nav-item">ログイン情報</li>
                    </a>
                    <a href="../login/logoutPage.php">
                        <li class="header-nav-item">ログアウト</li>
                    </a>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main contact_mg">

        <h1 class="detail_title">学生情報詳細　　　　<?= set_valid_status($result['valid_status_id']) ?></h1>

        <div class="back_inquiry_all">
            <a href="contact.php?id=<?= $agent_id ?>">&laquo;&nbsp;<?= $agent['insert_company_name'] ?>問い合わせ一覧に戻る</a>
        </div>
        <table class="students_detail" border="1" width="90%">
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">申込日時</th>
                <td><?php echo date("Y/m/d H:i:s", strtotime($result['created'])) ?></td>
            </tr>
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">氏名</th>
                <td><?php echo h($result['name']) ?>
                    <?php foreach ($duplicated_names as $d_name) : if ($d_name['id'] !=  $id) : ?>
                            <span style="background-color:red;">id<?= $d_name['id']; ?>と重複</span>
                    <?php endif;
                    endforeach ?>
                </td>
            </tr>
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">メールアドレス</th>
                <td><?php echo h($result['email']) ?>
                    <?php foreach ($duplicated_emails as $d_email) : if ($d_email['id'] !=  $id) : ?>
                            <span style="background-color:red;">id<?= $d_email['id']; ?>と重複</span>
                    <?php endif;
                    endforeach ?>
                </td>
            </tr>
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">電話番号</th>
                <td><?php echo h($result['tel']) ?>
                    <?php foreach ($duplicated_tels as $d_tel) : if ($d_tel['id'] !=  $id) : ?>
                            <span style="background-color:red;">id<?= $d_tel['id']; ?>と重複</span>
                    <?php endif;
                    endforeach ?>
                </td>
            </tr>
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">大学</th>
                <td><?php echo h($result['collage']) ?></td>
            </tr>
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">学科</th>
                <td><?php echo h($result['department']) ?></td>
            </tr>
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">何年卒</th>
                <td><?php echo h($result['class_of']) ?></td>
            </tr>
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">住所</th>
                <td><?php echo h($result['address']) ?></td>
            </tr>
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">備考欄</th>
                <td><?php echo h($result['memo']) ?>
            </tr>
            <tr bgcolor="white">
                <th bgcolor="#4FA49A">問い合わせID</th>
                <td><?php echo $id ?></td>
            </tr>
        <?php if (!empty(h($invalid_requests['invalid_request_memo']))) : ?>
                <tr bgcolor="white">
                    <th class="notice">通報内容</th>
                    <td><?php echo h($invalid_requests['invalid_request_memo']) ?></td>
                </tr>
        <?php endif; ?>
        <?php if ($result['valid_status_id'] === 3) : ?>
                <tr>
                    <th class="invalid">無効承認理由</th>
                    <td><?php echo h($result['reason']) ?></td>
                </tr>
        <?php endif; ?>
        <?php if ($result['valid_status_id'] === 4) : ?>
                <tr>
                    <th class="non_invalid" 	>無効申請拒否理由</th>
                    <td><?php echo h($result['reason']) ?></td>
                </tr>
        <?php endif; ?>
        </table>
        <?php if ($result['valid_status_id'] === 1||$result['valid_status_id'] === 2) : ?>
        <form action="" method="post" enctype="multipart/form-data">
        <p class= "invalid_operation"><label>無効化処理：<br>
            <textarea name="naiyou" cols="70" rows="5" required placeholder="処理理由を記入（ボタンを押すとエージェント企業へ自動送信されます。）"></textarea>
        </label></p>
                <input type="submit" class="make_invalid invalid" name="invalid" value="承認">
            <?php if ($result['valid_status_id'] === 2) : ?>
                <input type="submit" class="make_invalid non_invalid" name="non_invalid" value="拒否">
            <?php endif; ?>
        </form>
        <?php endif; ?>
    </main>
</body>
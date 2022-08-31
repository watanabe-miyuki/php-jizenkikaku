<?php 
require($_SERVER['DOCUMENT_ROOT'] . "/db_connect.php");
session_start();

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"]) || !isset($_SESSION['corporate_name'])) {
    header("Location: agent_login.php");
    exit();
}

$id = $_SESSION['id'];

try {
    $db = new PDO("mysql:host=db; dbname=shukatsu; charset=utf8", "$user", "$password");
    $stmt = $db->prepare('SELECT * FROM agents AS A WHERE A.id = :id');
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch();
} catch (PDOException $e) {
    echo "もう一回";
    $msg = $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録情報</title>
    <link rel="stylesheet" href="reset.css">
    <link rel="stylesheet" href="table.css">
    <link rel="stylesheet" href="agent_students_all.css">
    <link rel="stylesheet" href="agent_information.css">
</head>

<body>
    <header>
        <h1>
            <p><span>CRAFT</span>by boozer</p>
        </h1>
        <p class="welcome_agent">ようこそ　<?php echo ($result['corporate_name']); ?>様</p>
        <nav class="nav">
            <ul>
                <li><a href="agent_students_all.php">学生情報一覧</a></li>
                <li><a href="agent_information.php">登録情報</a></li>
                <li><a href="../index.php" target="_blank">ユーザー画面へ</a></li>
                <li><a href="agent_logoutPage.php">ログアウト</a></li>
            </ul>
        </nav>
    </header>
    <div class="all_wrapper">
        
        <div class="right_wrapper">
            <div class="manager_information">
                <h1 class="detail_title">担当者様登録情報</h1>
                <table class="agent_detail" border="1" width="90%">
                    <tr>
                        <th>担当者様氏名</th>
                        <td><?php echo ($result['client_name']); ?></td>
                    </tr>
                    <tr>
                        <th>担当者様部署名</th>
                        <td><?php echo ($result['client_department']); ?></td>
                    </tr>
                    <tr>
                        <th>担当者様メールアドレス</th>
                        <td><?php echo ($result['client_email']); ?></td>
                    </tr>
                    <tr>
                        <th>担当者様電話番号</th>
                        <td><?php echo ($result['client_tel']); ?></td>
                    </tr>
                </table>
            </div>
            <div class="company_information">
                <h1 class="company_information_title">登録情報</h1>
                <table class="agent_detail" border="1" width="90%">
                    <tr>
                        <th>企業名</th>
                        <td><?php echo ($result['corporate_name']); ?></td>
                    </tr>
                    <tr>
                        <th>貴社名</th>
                        <td><?php echo ($result['insert_company_name']); ?></td>
                    </tr>
                    <tr>
                        <th>掲載開始日時</th>
                        <td><?php echo date("Y/m/d", strtotime($result['started_at'])); ?></td>
                    </tr>
                    <tr>
                        <th>掲載終了日時</th>
                        <td><?php echo date("Y/m/d", strtotime($result['ended_at'])); ?></td>
                    </tr>
                    <tr>
                        <th>ログイン用メールアドレス</th>
                        <td><?php echo ($result['login_email']); ?></td>
                    </tr>
                    <tr>
                        <th>ログイン用パスワード</th>
                        <td>非表示</td>
                        <!-- <td><?php echo ($result['login_pass']); ?></td> -->
                    </tr>
                    <tr>
                        <th>情報送信先メールアドレス</th>
                        <td><?php echo ($result['to_send_email']); ?></td>
                    </tr>
                    <tr>
                        <th>おすすめポイント①</th>
                        <td><?php echo ($result['insert_recommend_1']); ?></td>
                    </tr>
                    <tr>
                        <th>おすすめポイント②</th>
                        <td><?php echo ($result['insert_recommend_2']); ?></td>
                    </tr>
                    <tr>
                        <th>おすすめポイント③</th>
                        <td><?php echo ($result['insert_recommend_3']); ?></td>
                    </tr>
                    <tr>
                        <th>取扱企業数</th>
                        <td><?php echo ($result['insert_handled_number']); ?></td>
                    </tr>
                </table>
            </div>
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
<?php require($_SERVER['DOCUMENT_ROOT'] . "/db_connect.php");
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
    <link rel="stylesheet" href="agent_logout.css">
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
    <div class="message">
        <p>ログアウトしますか？</p>
        <a href="agent_logout.php">ログアウトする</a>
    </div>
</body>

</html>
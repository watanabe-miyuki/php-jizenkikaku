<?php
session_start();
$output = '';
if (!isset($_SESSION["login"])) {
    header("Location: agent_login.php");
}
//セッション変数のクリア
$_SESSION = array();
//セッションクッキーも削除
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
//セッションクリア
@session_destroy();

echo $output;
?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>ログアウトページ</title>
    <link rel="stylesheet" href="agent_logout.css">
    <link rel="stylesheet" href="table.css">
</head>

<body>
    <header>
        <h1>
            <p><span>CRAFT</span>by boozer</p>
        </h1>
        <p class="agent_login">ログアウト画面</p>
    </header>
    <div class="message">
        <p>ログアウトしました</p>
        <a href="agent_login.php">ログインページへ</a>
    </div>
</body>

</html>
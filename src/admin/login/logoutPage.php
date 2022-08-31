<?php
require('../../db_connect.php');
session_start();
//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit();
}

//管理者ログイン情報
$stmt = $db->query('select * from admin_login;');
$admin_login = $stmt->fetch(PDO::FETCH_ASSOC);

// エージェントログイン情報
$stmt = $db->query('select * from agents;');
$agents_login = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>AgentList</title>
  <link rel="stylesheet" href="../css/reset.css" />
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../../agent/agent_logout.css">

<body>
  <header>
    <div class="header-inner">
      <h1 class="header-title">CRAFT管理者画面</h1>
      <nav class="header-nav">
        <ul class="header-nav-list">
          <a href="../index.php">
            <li class="header-nav-item">エージェント一覧</li>
          </a>
          <a href="../add/agentAdd.php">
            <li class="header-nav-item">エージェント追加</li>
          </a>
          <a href="../tags/tagsEdit.php">
            <li class="header-nav-item">タグ編集</li>
          </a>
          <a href="../login/loginInfo.php">
            <li class="header-nav-item ">ログイン情報</li>
          </a>
          <a href="../login/logoutPage.php">
            <li class="header-nav-item select">ログアウト</li>
          </a>
        </ul>
      </nav>
    </div>
  </header>
    <div class="message">
        <p>ログアウトしますか？</p>
        <a href="../login/logout.php">ログアウトする</a>
    </div>
</body>

</html>
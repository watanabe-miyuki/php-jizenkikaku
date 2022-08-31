<?php
session_start();
require('../../db_connect.php');

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
  header("Location: ../login/login.php");
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
            <li class="header-nav-item select">ログイン情報</li>
          </a>
          <a href="../login/logoutPage.php">
            <li class="header-nav-item">ログアウト</li>
          </a>
        </ul>
      </nav>
    </div>
  </header>
  <main class="main">
    <div class="agent-add-table">
      <table class="tags-add">
        <tr>
          <th>管理者</th>
        </tr>
        <tr>
          <td class="sub-th">企業名</td>
          <td class="sub-th">email</td>
          <td class="sub-th">pass</td>
          <td class="sub-th">編集</td>
        </tr>
        <tr>
          <td>
            boozer
          </td>
          <td>
            <!-- email -->
            <?= $admin_login['email'] ?>
          </td>
          <td>
            <!-- ここにパスワード -->
            【表示されません】
          </td>
          <td>
            <a href="loginUpdate.php?id=admin">編集</a>
          </td>
        </tr>
      </table>
      <table class="tags-add">
        <tr>
          <th>エージェント</th>
        </tr>
        <tr>
          <td class="sub-th">企業名</td>
          <td class="sub-th">email</td>
          <td class="sub-th">pass</td>
          <td class="sub-th">編集</td>
        </tr>
        <?php foreach ($agents_login as $a_login) : ?>
          <tr>
            <td>
              <?= $a_login['insert_company_name'] ?>
            </td>
            <td>
              <?= $a_login['login_email'] ?>
            </td>
            <td>
              【表示されません】
            </td>
            <td>
              <a href="loginUpdate.php?id=<?= $a_login['id'] ?>">編集</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </main>
</body>

</html>
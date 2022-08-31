<?php
require('../../db_connect.php');
session_start();
//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
  header("Location: login.php");
  exit();
}

$id = $_GET['id'];
if($id=== 'admin'){
  $title = '管理者';
  $name= 'CRAFT';
  $stmt = $db->query('select * from admin_login;');
  $stmt->execute();
  $admin = $stmt->fetch(PDO::FETCH_ASSOC);
  $email = $admin['email'];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $form['login_email'] = filter_input(INPUT_POST, 'login_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $form['login_pass'] = filter_input(INPUT_POST, 'login_pass', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $login_pass = password_hash($form['login_pass'], PASSWORD_DEFAULT);
    $stmt = $db->prepare('update admin_login set email = :login_email, login_password = :login_pass');
    $stmt->bindValue('login_email', $form['login_email'], PDO::PARAM_STR);
    $stmt->bindValue('login_pass', $login_pass, PDO::PARAM_STR);
    if (!$stmt) {
      die($db->error);
    }
    $success = $stmt->execute();
    if (!$success) {
      die($db->error);
    }

    header('location: loginInfo.php');
  }//post


}else{//agent
  $title  = 'エージェント';
  $stmt = $db->prepare('select * from agents where id = :id;');
  $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
  $stmt->execute();
  $agent = $stmt->fetch(PDO::FETCH_ASSOC);
  $name = $agent['insert_company_name'];
  $email = $agent['login_email'];

  $error = [];
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

      $form['login_email'] = filter_input(INPUT_POST, 'login_email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $form['login_pass'] = filter_input(INPUT_POST, 'login_pass', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  // login_emailの重複チェック
  if ($form['login_email'] != '') {
    $stmt = $db->prepare('select count(*) from agents where login_email=:login_email and id != :id');
    if (!$stmt) {
      die($db->error);
    }
    $stmt->bindValue('login_email', $form['login_email'], PDO::PARAM_STR);
    $stmt->bindValue('id', $id, PDO::PARAM_INT);
    $success = $stmt->execute();
    $cnt = (int)$stmt->fetchColumn();
    if ($cnt > 0) {
      $error['login_email'] = 'duplicate';
    }
  }
  if (empty($error)) {
    $login_pass = password_hash($form['login_pass'], PASSWORD_DEFAULT);
    $stmt = $db->prepare('update agents set login_email = :login_email, login_pass = :login_pass where id = :id');
    $stmt->bindValue('login_email', $form['login_email'], PDO::PARAM_STR);
    $stmt->bindValue('login_pass', $login_pass, PDO::PARAM_STR);
    $stmt->bindValue('id', (int)$id, PDO::PARAM_INT);
    if (!$stmt) {
      die($db->error);
    }
    $success = $stmt->execute();
    if (!$success) {
      die($db->error);
    }

    header('location: loginInfo.php');
  }//!error
  }//post
}//agent

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
            <li class="header-nav-item">タグ一覧</li>
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
  <form action="" method="post" enctype="multipart/form-data">
    <div class="agent-add-table">
      <table class="tags-add">
        <tr>
          <th><?= $title ?></th>
        </tr>
        <tr>
          <td class="sub-th">企業名</td>
          <td class="sub-th">email</td>
          <td class="sub-th">pass</td>
          <td class="sub-th">編集</td>
        </tr>
        <tr>
          <td>
            <?php echo $name ?>
          </td>
          <td>
            <input type="email" name="login_email" value="<?php echo h($email); ?>" required/>
            <?php if (isset($error['login_email']) && $error['login_email'] === 'duplicate') : ?>
                <p class="error">* 指定されたメールアドレスはすでに登録されています</p><?php endif; ?>
          </td>
          <td>
          <input type="password" name="login_pass" value="" required/>
          </td>
          <td>
          <input type="submit" value="編集を完了する" />
          </td>
        </tr>
      </table>
    </div>
    </form>
  </main>
</body>

</html>
<?php
session_start();
require('../../db_connect.php');

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
    header("Location: ../login/login.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'sortsAdd' && isset($_SESSION['sort_id'])) {
  $sort_id = $_SESSION['sort_id'];
  unset($_SESSION['sort_id']);
}

// 絞り込みの種類情報
$stmt = $db->query('select * from filter_sorts;');
$filter_sorts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query('select * from filter_tags;');
$filter_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 絞り込みの種類追加
$stmt = $db->prepare('insert into filter_tags (sort_id, tag_name) VALUES (:sort_id, :tag_name)');
$stmt->bindValue(':sort_id', $_POST['sort_id'], PDO::PARAM_STR);
$stmt->bindValue(':tag_name', $_POST['tag_name'], PDO::PARAM_STR);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt->execute();
  // header('location: tagEditThanks.php');
  header('location: tagsEdit.php');
}
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
</head>

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
            <li class="header-nav-item select">タグ編集</li>
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
  <p>
  <main class="main">
  <h1 class="main-title">タグ追加画面</h1>
  <form action="" method="post" enctype="multipart/form-data">
    <div class="agent-add-table">

      <table class="tags-add">
        <tr>
        <p class="error">
        * タグのない絞り込みの種類は、反映されないので、必ずタグをつけてください。</br>
        </p>
          <th>絞り込みの種類</th>
        </tr>
        <tr>
          <td class="sub-th">番号</td>
          <td class="sub-th">名前</td>
        </tr>
        <?php foreach ($filter_sorts as $filter_sort) : ?>
          <tr>
            <td>
              <?php echo $filter_sort['id'] ?>
            </td>
            <td>
              <?= $filter_sort['sort_name']; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
      <table class="tags-add">
        <tr>
          <th>タグ</th>
        </tr>
        <tr>
          <td class="sub-th">絞り込みの種類の番号</td>
          <td class="sub-th">名前</td>
        </tr>
        <?php foreach ($filter_tags as $filter_tag) : ?>
          <tr>
            <td>
              <?php echo $filter_tag['sort_id'] ?>
            </td>
            <td>
              <?= $filter_tag['tag_name']; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td>
            <?php if(isset($sort_id)): echo $sort_id;?>
            <input type="hidden" name="sort_id" value="<?=$sort_id?>" />
          <?php else: ?>
            <input type="number" name="sort_id" value="" placeholder="絞り込み種類の番号"required/>
            <?php endif; ?>
          </td>
          <td>
            <input type="text" name="tag_name" value="" placeholder="追加するタグを記入" required/>
          </td>
        </tr>
      </table>
      <div>
        <?php if(!isset($sort_id)): ?><a href="tagsEdit.php">&laquo;&nbsp;タグ編集に戻る</a> | <?php endif; ?><input type="submit" value="追加する" /></div>
    </div>
    </form>

  </main>
</body>

</html>
<?php
session_start();
require('../../db_connect.php');

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
    header("Location: ../login/login.php");
    exit();
}


// 絞り込みの種類
$stmt = $db->query('select * from filter_sorts;');
$filter_sorts = $stmt->fetchAll(PDO::FETCH_ASSOC);
// タグ
$stmt = $db->query('select * from filter_tags;');
$filter_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 削除機能
// 絞り込みの種類
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// エラーになるから別々でやろう
if($_POST['filter_sorts'] != ''){
$stmt = $db->prepare('delete from filter_sorts where id = :id');
foreach($_POST['filter_sorts'] as $filter_sort):
$stmt->bindValue(':id', $filter_sort, PDO::PARAM_INT);
$stmt->execute();
endforeach;
$stmt = $db->prepare('delete from filter_tags where sort_id = :sort_id');
foreach($_POST['filter_sorts'] as $filter_sort):
$stmt->bindValue(':sort_id', $filter_sort, PDO::PARAM_INT);
$stmt->execute();
endforeach;
}

// タグ
if($_POST['filter_tags'] != ''){
$stmt = $db->prepare('delete from filter_tags where tag_id = :tag_id');
foreach($_POST['filter_tags'] as $filter_tag):
$stmt->bindValue(':tag_id', $filter_tag, PDO::PARAM_INT);
$stmt->execute();
endforeach;
}

header('location: tagEditThanks.php');
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
    <div class="agent-add-table">
    <form action="" method="post" enctype="multipart/form-data">
      <table class="tags-add">
      <p class="error">* 絞り込みの種類を削除すると付属するタグも削除されます</p>
        <tr>
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
              <label class="filter-sort">
                <input type="checkbox" name="filter_sorts[]" value="<?= $filter_sort['id']; ?>" />
                <span><?= $filter_sort['sort_name']; ?></span></label>
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
              <label class="filter-tag">
                <input type="checkbox" name="filter_tags[]" value="<?= $filter_tag['tag_id']; ?>" />
                <span><?= $filter_tag['tag_name']; ?></span></label>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <div><a href="tagsEdit.php">&laquo;&nbsp;タグ編集に戻る</a> | <input type="submit" value="削除する" /></div>
    </form>
    </div>
  </main>
</body>

</html>
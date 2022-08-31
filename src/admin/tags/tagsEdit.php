<?php
session_start();
require('../../db_connect.php');

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
  header("Location: ../login/login.php");
  exit();
}

//タグ情報
$stmt = $db->query('select fs.id, sort_name, tag_id, tag_name from filter_sorts fs inner join filter_tags ft on fs.id = ft.sort_id;
');
$filter_sorts_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
$t_list = [];
foreach ($filter_sorts_tags as $f) {
  $t_list[(int)$f['id']][] = $f;
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
  <main class="main">
  <h1 class="main-title">掲載中の絞り込み</h1>
    <div class="agent-add-table">
      <table class="tags-add">
      <p><span class="error">絞り込みの種類、タグ両方が設定されていない場合、掲載されません。<br>片方しかなものは、[編集]から設定してください。</span></p>
        <tr>
          <td class="sub-th">絞り込みの種類</td>
          <td class="sub-th">タグ</td>
        </tr>
        <?php foreach ($t_list as $filter_sort) : ?>
          <tr>
            <td><?= current($filter_sort)['sort_name']; ?></td>
            <td>
              <?php foreach ($filter_sort as $filter_tag) : ?>
                <label class="added-tag">
                  <span><?= $filter_tag['tag_name']; ?></span> </label>
              <?php endforeach; ?>

            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
    <div class="tag_edit_btns">
      <button onclick="location.href='tagsUpdate.php'">編集</button>
      <button onclick="location.href='sortsAdd.php'">絞り込みの種類追加</button>
      <button onclick="location.href='tagsAdd.php'">タグ追加</button>
      <button onclick="location.href='tagsDelete.php'">選択して削除</button>
    </div>
      
  </main>
</body>

</html>
<?php

session_start();
require('../../db_connect.php');

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
    header("Location: ../login/login.php");
    exit();
}

// 絞り込みの種類情報
$stmt = $db->query('select * from filter_sorts;');
$filter_sorts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $db->query('select * from filter_tags;');
$filter_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $args = array(
    'filter_sorts' => array(
      // ここfilter_sortに名前を変えたい
      'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
      'flags'     => FILTER_REQUIRE_ARRAY,
    ),
    'filter_tags' => array(
      'filter' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
      'flags'     => FILTER_REQUIRE_ARRAY,
    ),
  );

  $form = filter_input_array(INPUT_POST, $args);
  // 絞り込みの種類
  $stmt = $db->prepare('update filter_sorts set sort_name=:sort_name where id=:id');
  foreach ($form['filter_sorts'] as $sort) :
    $stmt->bindValue('sort_name', $sort['name'], PDO::PARAM_STR);
    $stmt->bindValue('id', $sort['id'], PDO::PARAM_INT);
    if (!$stmt) {
      die($db->error);
    }
    $success = $stmt->execute();
    if (!$success) {
      die($db->error);
    }
  endforeach;
  // タグ
  $stmt = $db->prepare('update filter_tags set sort_id=:sort_id, tag_name=:tag_name where tag_id=:tag_id');
  foreach ($form['filter_tags'] as $tag) :
    $stmt->bindValue('sort_id', $tag['sort_id'], PDO::PARAM_STR);
    $stmt->bindValue('tag_name', $tag['name'], PDO::PARAM_INT);
    $stmt->bindValue('tag_id', $tag['tag_id'], PDO::PARAM_INT);
    if (!$stmt) {
      die($db->error);
    }
    $success = $stmt->execute();
    if (!$success) {
      die($db->error);
    }
  endforeach;

  header('location: tagEditThanks.php');
  exit();
}
// }

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
  <h1 class="main-title">絞り込み編集画面</h1>
    <form action="" method="post" enctype="multipart/form-data">
      <div class="agent-add-table">
      <p class="error">* 番号は半角英数字で入力してください。</p>
        <table class="tags-add">
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
                <input type="hidden" name="filter_sorts[<?= $filter_sort['id'] ; ?>][id]" value="<?= $filter_sort['id']; ?>" />
              </td>
              <td>
                <span>
                  <input type="text" name="filter_sorts[<?= $filter_sort['id'] ; ?>][name]" value="<?= $filter_sort['sort_name']; ?>" />
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
        <table class="tags-add">
        
        <p class="error">
        * 入力した番号にあう絞り込みの種類がなければ、ユーザー画面に表示されません。</br>
        * 絞り込みの種類と番号が一致しないタグは手動で削除してください。
        </p>
          <tr>
            <th>タグ</th>
            <!-- ここ整える -->
          </tr>
          <tr>
            <td class="sub-th">絞り込みの種類の番号</td>
            <td class="sub-th">名前</td>
          </tr>
          <?php foreach ($filter_tags as $filter_tag) : ?>
            <tr>
              <td>
                <input type="number" name="filter_tags[<?php echo $filter_tag['tag_id'] ?>][sort_id]" value="<?php echo $filter_tag['sort_id'] ?>" />
                <input type="hidden" name="filter_tags[<?php echo $filter_tag['tag_id'] ?>][tag_id]" value="<?php echo $filter_tag['tag_id'] ?>" />
              </td>
              <td>
                <input type="text" name="filter_tags[<?php echo $filter_tag['tag_id'] ?>][name]" value="<?= $filter_tag['tag_name']; ?>" />
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
        
      <div><a href="tagsEdit.php">&laquo;&nbsp;タグ編集に戻る</a> | <input type="submit" value="編集を完了する" /></div>
    </form>
  </main>
</body>

</html>
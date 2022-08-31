<?php
session_start();
require('../../db_connect.php');

// //ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
    header("Location: ../login/login.php");
    exit();
}

$id = $_GET['id'];
if (isset($_SESSION['form'])) {
  $form = $_SESSION['form'];
  // 期間判定
  date_default_timezone_set('Asia/Tokyo');
  $today = date("Y-m-d"); //今日の日付
  $started_at = $form['started_at'];
  $ended_at = $form['ended_at'];
  if (strtotime($today) < strtotime($started_at) || strtotime($today) > strtotime($ended_at)){
    $form['list_status'] = 2;
  } elseif (strtotime($today) >= strtotime($started_at) && strtotime($ended_at) >= strtotime($today)) {
    $form['list_status'] = 1;
  }
  // var_dump($form);
} else {
  header('location: ../../index.php');
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $db->prepare('update agents set corporate_name = :corporate_name, started_at = :started_at, ended_at = :ended_at, to_send_email = :to_send_email, application_max = :application_max, charge = :charge, client_name = :client_name, client_department = :client_department, client_email = :client_email, client_tel = :client_tel, insert_company_name = :insert_company_name, insert_logo = :insert_logo, insert_recommend_1 = :insert_recommend_1, insert_recommend_2 = :insert_recommend_2, insert_recommend_3 = :insert_recommend_3, insert_handled_number = :insert_handled_number, list_status = :list_status where id = :id');
  $stmt->bindValue('corporate_name', $form['corporate_name'], PDO::PARAM_STR);
  $started_at = new DateTime( $form['started_at']);
  $stmt->bindValue('started_at', $started_at->format('Y-m-d'), PDO::PARAM_STR);
  $ended_at = new DateTime( $form['ended_at']);
  $stmt->bindValue('ended_at', $ended_at->format('Y-m-d'), PDO::PARAM_STR);
  $stmt->bindValue('to_send_email', $form['to_send_email'], PDO::PARAM_STR);
  $stmt->bindValue('application_max', $form['application_max'], PDO::PARAM_INT);
  $stmt->bindValue('charge', $form['charge'], PDO::PARAM_INT);
  $stmt->bindValue('client_name', $form['client_name'], PDO::PARAM_STR);
  $stmt->bindValue('client_department', $form['client_department'], PDO::PARAM_STR);
  $stmt->bindValue('client_email', $form['client_email'], PDO::PARAM_STR);
  $stmt->bindValue('client_tel', $form['client_tel'], PDO::PARAM_STR);
  $stmt->bindValue('insert_company_name', $form['insert_company_name'], PDO::PARAM_STR);
  $stmt->bindValue('insert_logo', $form['insert_logo'], PDO::PARAM_STR);
  $stmt->bindValue('insert_recommend_1', $form['insert_recommend_1'], PDO::PARAM_STR);
  $stmt->bindValue('insert_recommend_2', $form['insert_recommend_2'], PDO::PARAM_STR);
  $stmt->bindValue('insert_recommend_3', $form['insert_recommend_3'], PDO::PARAM_STR);
  $stmt->bindValue('insert_handled_number', $form['insert_handled_number'], PDO::PARAM_STR);
  $stmt->bindValue('list_status', $form['list_status'], PDO::PARAM_INT);
  $stmt->bindValue('id', (int)$id, PDO::PARAM_INT);
  if (!$stmt) {
    die($db->error);
  }
  $success = $stmt->execute();
  if (!$success) {
    die($db->error);
  }


  // タグについて、全て削除した後、改めてinsert
  // 削除
  $stmt = $db->prepare('delete from agents_tags where agent_id = :agent_id');
$stmt->bindValue(':agent_id', (int)$id, PDO::PARAM_INT);
$success = $stmt->execute();
if (!$success) {
  die($db->error);
}

if($form['agent_tags']){
$stmt = $db->prepare('insert into agents_tags (agent_id, tag_id) VALUES (:agent_id, :tag_id)');
foreach($form['agent_tags'] as $agent_tag):
$stmt->bindValue('agent_id', (int)$id, PDO::PARAM_INT);
$stmt->bindValue('tag_id', $agent_tag, PDO::PARAM_INT);
if (!$stmt) {
  die($db->error);
}
$success = $stmt->execute();
if (!$success) {
  die($db->error);
}
endforeach;
}

  unset($_SESSION['form']);
  header('location: ../index.php');
}

//タグ情報
$stmt = $db->query('select fs.id, sort_name, tag_id, tag_name from filter_sorts fs inner join filter_tags ft on fs.id = ft.sort_id;
');
$filter_sorts_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
$t_list = [];
foreach ($filter_sorts_tags as $f) {
  $t_list[(int)$f['id']][] = $f;
}

// エージェントタグ
$stmt = $db->prepare('select * from agents_tags where agent_id=:id');
$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
$stmt->execute();
$agent_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
  <script src="./js/jquery-3.6.0.min.js"></script>
  <script src="./js/script.js" defer></script>
</head>

<body>
  <header>
    <div class="header-inner">
      <h1 class="header-title">CRAFT管理者画面</h1>
      <nav class="header-nav">
        <ul class="header-nav-list">
        <a href="../index.php">
            <li class="header-nav-item select">エージェント一覧</li>
          </a>
          <a href="../add/agentAdd.php">
            <li class="header-nav-item">エージェント追加</li>
          </a>
          <a href="../tags/tagsEdit.php">
            <li class="header-nav-item">タグ編集</li>
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
    <h1 class="main-title"><?php echo h($form['insert_company_name']); ?>詳細 (<?php echo set_list_status($form['list_status']); ?>)</h1>
    <div class="agent-add-table">
      <form action="" method="post" enctype="multipart/form-data">
        <table class="main-info-table">
          <tr>
            <th>法人名</th>
            <td><?php echo h($form['corporate_name']) ?></td>
          </tr>
          <tr>
            <th>掲載状態</th>
            <td><label class="list-status">
                <input type="radio" name="list-status" value="1" <?php if ($form['list_status'] === 1) : ?>checked <?php endif; ?> disabled /><span>掲載</span>
              </label>
              <label class="list-status">
                <input type="radio" name="list-status" value="2" <?php if ($form['list_status'] != 1) : ?>checked <?php endif; ?> disabled /><span>非掲載</span>
              </label>
            </td>
          </tr>

          <tr>
            <th>掲載期間</th>
            <td>
              <?php echo date("Y/m/d", strtotime($form['started_at'])) ?> ～
              <?php echo date("Y/m/d", strtotime($form['ended_at'])) ?>
            </td>
          </tr>
          <tr>
            <th>学生情報送信先</th>
            <td><?php echo h($form['to_send_email']) ?></td>
          </tr>
          <tr>
            <th>申し込み上限数（/月）</th>
            <td><?php echo h($form['application_max']) ?> 件</td>
          </tr>
          <tr>
            <th>請求金額（/件）</th>
            <td><?php echo h($form['charge']) ?> 円</td>
          </tr>
        </table>

        <table class="contact-info-table">
          <tr>
            <th>担当者情報</th>
          </tr>
          <tr>
            <td class="sub-th">氏名</td>
            <td><?php echo h($form['client_name']) ?></td>
          </tr>
          <tr>
            <td class="sub-th">部署名</td>
            <td><?php echo h($form['client_department']) ?></td>
          </tr>
          <tr class="contact-number">
            <td class="sub-th">連絡先</td>
            <td>
              email:<?php echo h($form['client_email']) ?>　　　tel:<?php echo h($form['client_tel']) ?>
            </td>
          </tr>
        </table>
        <table class="post-info-table">
          <tr>
            <th>掲載情報</th>
          </tr>
          <tr>
            <td class="sub-th">掲載企業名</td>
            <td><?php echo h($form['insert_company_name']) ?></td>
          </tr>
          <tr>
            <td class="sub-th">企業ロゴ</td>
            <td><img src="../../img/insert_logo/<?php echo h($form['insert_logo']); ?>" width="300" alt="" /></td>
          </tr>
          <tr>
            <td class="sub-th">オススメポイント</td>
            <td>
              <ul>
                <li>・<?php echo h($form['insert_recommend_1']) ?></li>
                <li>・<?php echo h($form['insert_recommend_2']) ?></li>
                <li>・<?php echo h($form['insert_recommend_3']) ?></li>
              </ul>
            </td>
          </tr>
          <tr>
            <td class="sub-th">取扱い企業数</td>
            <td><?php echo h($form['insert_handled_number']) ?></td>
          </tr>
        </table>
        <table class="tags-add">
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
                    <input type="checkbox" name="agent_tags[]" value="<?= $filter_tag['tag_id'] ?>" disabled <?php if($form['agent_tags']):foreach ($form['agent_tags'] as $agent_tag) : if (h($filter_tag['tag_id']) === $agent_tag) : ?>checked <?php endif;
                                                                                                                                                                              endforeach;endif;?> />
                    <span><?= $filter_tag['tag_name']; ?></span> </label>
                <?php endforeach; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>
        <div><a href="update.php?id=<?=$id?>&action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
      </form>
    </div>
  </main>
</body>

</html>
<?php
require('../db_connect.php');

session_start();
//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
  header("Location: login/login.php");
  exit();
}

$id = $_GET['id'];

//エージェント情報
$stmt = $db->prepare('select * from agents where id = :id');
$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
$stmt->execute();
$agent = $stmt->fetch(PDO::FETCH_ASSOC);

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
  <link rel="stylesheet" href="./css/reset.css" />
  <link rel="stylesheet" href="./css/style.css" />
</head>

<body>
  <header>
    <div class="header-inner">
      <h1 class="header-title">CRAFT管理者画面</h1>
      <nav class="header-nav">
        <ul class="header-nav-list">
          <a href="./index.php">
            <li class="header-nav-item select">エージェント一覧</li>
          </a>
          <a href="./add/agentAdd.php">
            <li class="header-nav-item">エージェント追加</li>
          </a>
          <a href="./tags/tagsEdit.php">
            <li class="header-nav-item">タグ編集</li>
          </a>
          <a href="./login/loginInfo.php">
            <li class="header-nav-item">ログイン情報</li>
          </a>
          <a href="./login/logoutPage.php">
            <li class="header-nav-item">ログアウト</li>
          </a>
        </ul>
      </nav>
    </div>
  </header>
  <main class="main">
    <h1 class="main-title"><?php echo h($agent['insert_company_name']); ?>詳細 (<?php echo set_list_status($agent['list_status']); ?>)</h1>
    <div class="operations">
      <!-- <a href="./index.php">エージェント一覧へ戻る＞</a> -->
      <button onclick="location.href='./index.php'">一覧画面へ戻る</button>
      <button onclick="location.href='./update/update.php?id=<?= $id ?>'">編集</a></button>
      <!-- <button><a href="./update/update.php?id=<?= $id ?>">編集</a></button> -->
      <button onclick="window.open('../index.php','_blank')">ユーザー画面を確認</button>
    </div>
    <div class="agent-add-table">
      <table class="main-info-table">
        <tr>
          <th>法人名</th>
          <td><?php echo h($agent['corporate_name']) ?></td>
        </tr>
        <tr>
          <th>掲載状態</th>
          <td><label class="list-status">
              <input type="radio" name="list-status" value="1" <?php if ($agent['list_status'] === 1) : ?>checked <?php endif; ?> disabled /><span>掲載中</span>
            </label>
            <label class="list-status">
              <input type="radio" name="list-status" value="2" <?php if ($agent['list_status'] != 1) : ?>checked <?php endif; ?> disabled /><span>掲載停止中</span>
            </label>
          </td>
        </tr>

        <tr>
          <th>掲載期間</th>
          <td>
            <?php echo date("Y/m/d", strtotime($agent['started_at'])) ?> ～
            <?php echo date("Y/m/d", strtotime($agent['ended_at'])) ?>
          </td>
        </tr>

        <tr class="login-info">
          <th>ログイン情報</th>
          <td>
            email:<?php echo h($agent['login_email']) ?>　　　pass: 【表示されません】

          </td>
        </tr>

        <tr>
          <th>学生情報送信先</th>
          <td><?php echo h($agent['to_send_email']) ?></td>
        </tr>
        <tr>
          <th>申し込み上限数（/月）</th>
          <td><?php echo h($agent['application_max']) ?> 件</td>
        </tr>
        <tr>
          <th>請求金額（/件）</th>
          <td><?php echo h($agent['charge']) ?> 円</td>
        </tr>
      </table>
      <table class="contact-info-table">
        <tr>
          <th>担当者情報</th>
        </tr>
        <tr>
          <td class="sub-th">氏名</td>
          <td><?php echo h($agent['client_name']) ?></td>
        </tr>
        <tr>
          <td class="sub-th">部署名</td>
          <td><?php echo h($agent['client_department']) ?></td>
        </tr>
        <tr class="contact-number">
          <td class="sub-th">連絡先</td>
          <td>
            email:<?php echo h($agent['client_email']) ?>　　　tel:<?php echo h($agent['client_tel']) ?>
          </td>
        </tr>
      </table>
      <table class="post-info-table">
        <tr>
          <th>掲載情報</th>
        </tr>
        <tr>
          <td class="sub-th">掲載企業名</td>
          <td><?php echo h($agent['insert_company_name']) ?></td>
        </tr>
        <tr>
          <td class="sub-th">企業ロゴ</td>
          <td><img src="../../img/insert_logo/<?php echo h($agent['insert_logo']); ?>" width="300" alt="" /></td>
        </tr>
        <tr>
          <td class="sub-th">オススメポイント</td>
          <td>
            <ul>
              <li>・<?php echo h($agent['insert_recommend_1']) ?></li>
              <li>・<?php echo h($agent['insert_recommend_2']) ?></li>
              <li>・<?php echo h($agent['insert_recommend_3']) ?></li>
            </ul>
          </td>
        </tr>
        <tr>
          <td class="sub-th">取扱い企業数</td>
          <td><?php echo h($agent['insert_handled_number']) ?></td>
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

                  <input type="checkbox" name="filter_tag" disabled <?php foreach ($agent_tags as $agent_tag) : if ($filter_tag['tag_id'] === $agent_tag['tag_id']) : ?>checked <?php endif;
                                                                                                                                                                            endforeach; ?> />
                  <span><?= $filter_tag['tag_name']; ?></span> </label>
              <?php endforeach; ?>

            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </main>
</body>

</html>
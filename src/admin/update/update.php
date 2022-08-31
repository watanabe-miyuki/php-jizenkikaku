<?php
session_start();
require('../../db_connect.php');

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
  header("Location: ../login/login.php");
  exit();
}

$id = $_GET['id'];

if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
  $agent = $_SESSION['form'];
  $agent_tags = $agent['agent_tags'];
} elseif (isset($agent)) { //POSTの中身
  $agent_tags = $agent['agent_tags'];
}else{
  //エージェント情報
  $stmt = $db->prepare('select * from agents where id = :id');
  $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
  $stmt->execute();
  $agent = $stmt->fetch(PDO::FETCH_ASSOC);
  // エージェントタグ
  $stmt = $db->prepare('select tag_id from agents_tags where agent_id=:id');
  $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
  $stmt->execute();
  $agent_tags = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
// 写真変更ないときは既存のものをセッションに渡す
$filename = $agent['insert_logo'];



//タグ情報
$stmt = $db->query('select fs.id, sort_name, tag_id, tag_name from filter_sorts fs inner join filter_tags ft on fs.id = ft.sort_id;
');
$filter_sorts_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
$t_list = [];
foreach ($filter_sorts_tags as $f) {
  $t_list[(int)$f['id']][] = $f;
}


$error = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $args = array(
    'corporate_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'started_at' => FILTER_SANITIZE_NUMBER_INT,
    'ended_at' => FILTER_SANITIZE_NUMBER_INT,
    'to_send_email' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'application_max' => FILTER_SANITIZE_NUMBER_INT,
    'charge' => FILTER_SANITIZE_NUMBER_INT,
    'client_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'client_department' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'client_email' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'client_tel' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'insert_company_name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'insert_recommend_1' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'insert_recommend_2' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'insert_recommend_3' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'insert_handled_number' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'agent_tags' => array(
      'filter' => FILTER_SANITIZE_NUMBER_INT,
      'flags'     => FILTER_REQUIRE_ARRAY,
    ),
  ); // タグについては配列？
  $agent = filter_input_array(INPUT_POST, $args);
    $agent_tags = $agent['agent_tags'];


  // エラー判定
    // タグ選択必須テスト
    if (!$agent['agent_tags']) {
      $error['agent_tags'] = 'blank';
  }else{
    foreach($agent['agent_tags'] as $agent_tag){
      $stmt = $db->prepare('select sort_id from filter_tags where tag_id=:tag_id');
      $stmt->bindValue(':tag_id', $agent_tag, PDO::PARAM_STR);
      $stmt->execute();
      $tags[] = $stmt->fetch(PDO::FETCH_COLUMN);
      }
      foreach ($filter_sorts_tags as $f) {
        if(!in_array($f['id'], $tags)) {
          $error['agent_tags'] = 'blank';
      }
      }
  }

  if ($agent['started_at'] > $agent['ended_at']) {
    $error['period'] = 'reverse';
  }

  // 画像のチェック(変更は任意)
  $insert_logo = $_FILES['insert_logo'];
  if ($insert_logo['name'] !== '' && $insert_logo['error'] === 0) {
    $type = mime_content_type($insert_logo['tmp_name']);
    if ($type !== 'image/png' && $type !== 'image/jpeg') {
      $error['insert_logo'] = 'type';
    }
  }

  // エラーがなければ確認画面へ
  if (empty($error)) {
    $_SESSION['form'] = $agent;
    //画像のアップロード
    if ($insert_logo['name'] !== '') {
      $filename = date('YmdHis') . '_' . $insert_logo['name'];
      if (!move_uploaded_file($insert_logo['tmp_name'], '../../img/insert_logo/' . $filename)) {
        die('ファイルのアップロードに失敗しました');
      }
      $_SESSION['form']['insert_logo'] = $filename;
    } else {
      $_SESSION['form']['insert_logo'] = $filename;
    }
    header("location: updateCheck.php?id=$id");
    exit();
  }
}

?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>編集</title>
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
    <h1 class="main-title"><?php echo h($agent["insert_company_name"]); ?>編集画面</h1>
    <div class="agent-add-table">
      <form action="" method="post" enctype="multipart/form-data">
        <table class="main-info-table">
          <span class="error">*は必須項目</span>
          <tr>
            <th>法人名<span class="error">*</span></th>
            <td><input type="text" name="corporate_name" value="<?php echo h($agent["corporate_name"]); ?>" required /></td>
          </tr>
          <tr>
            <th>掲載状態</th>
            <td>
              【掲載期間と申し込み上限数で自動判定】
            </td>
          </tr>

          <tr>
            <th>掲載期間<span class="error">*</span></th>
            <td>
              <input type="date" name="started_at" value="<?php echo h($agent["started_at"]); ?>" required /> ～
              <input type="date" name="ended_at" value="<?php echo h($agent["ended_at"]); ?>" required />
              <?php if (isset($error['period']) && $error['period'] === 'reverse') : ?>
                <p class="error">* 終了日を開始日より後に設定してください。</p>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
          <tr>
            <th>学生情報送信先<span class="error">*</span></th>
            <td><input type="email" name="to_send_email" value="<?php echo h($agent["to_send_email"]); ?>" required />
          </tr>
          <th>申し込み上限数（/月）<span class="error">*</span></th>
          <td><input type="number" name="application_max" value="<?php echo h($agent["application_max"]); ?>" min="1" required /> 件
            </tr>
          <th>請求金額（/件）<span class="error">*</span></th>
          <td><input type="number" name="charge" value="<?php echo h($agent["charge"]); ?>" required /> 円
            </tr>
        </table>
        <table class="contact-info-table">
          <tr>
            <th>担当者情報</th>
          </tr>
          <tr>
            <td class="sub-th">氏名<span class="error">*</span></td>
            <td><input type="text" name="client_name" value="<?php echo h($agent["client_name"]); ?>" required /></td>
          </tr>
          <tr>
            <td class="sub-th">部署名<span class="error">*</span></td>
            <td><input type="text" name="client_department" value="<?php echo h($agent["client_department"]); ?>" required /></td>
          </tr>
          <tr class="contact-number">
            <td class="sub-th">連絡先<span class="error">*</span></td>
            <td>
              email:<input type="email" name="client_email" value="<?php echo h($agent["client_email"]); ?>" required />　　　tel:<input type="tel" name="client_tel" value="<?php echo h($agent["client_tel"]); ?>" required />
            </td>
          </tr>
        </table>
        <table class="post-info-table">
          <tr>
            <th>掲載情報</th>
          </tr>
          <tr>
            <td class="sub-th">掲載企業名<span class="error">*</span></td>
            <td>
              <input type="text" name="insert_company_name" value="<?php echo h($agent["insert_company_name"]); ?>" required /><?php if (isset($error['insert_company_name']) && $error['insert_company_name'] === 'blank') : ?>
                <p class="error">* 掲載する企業名を入力してください</p>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <td class="sub-th">企業ロゴ<span class="error">*</span></td>
            <td><input type="file" name="insert_logo" value="" />
              <?php if (isset($error['insert_logo']) && $error['insert_logo'] === 'type') : ?>
                <p class="error">* 写真などは「.png」または「.jpg」の画像を指定してください</p>
              <?php endif; ?>
              <p class="error">* ロゴを変更する場合は写真を選択してください</p>
            </td>
          </tr>
          <tr>
            <td class="sub-th">オススメポイント<span class="error">*</span></td>
            <td>
              <input type="text" name="insert_recommend_1" placeholder="100文字以内で入力してください" value="<?php echo h($agent["insert_recommend_1"]); ?>" required /><input type="text" name="insert_recommend_2" value="<?php echo h($agent["insert_recommend_2"]); ?>" required /><input type="text" name="insert_recommend_3" value="<?php echo h($agent["insert_recommend_3"]); ?>" required />
            </td>
          </tr>
          <tr>
            <td class="sub-th">取扱い企業数<span class="error">*</span></td>
            <td><input type="text" name="insert_handled_number" value="<?php echo h($agent["insert_handled_number"]); ?>" required /></td>
          </tr>
        </table>
        <table class="tags-add">
          <tr>
            <td class="sub-th">絞り込みの種類</td>
            <td class="sub-th">タグ<?php if (isset($error['agent_tags']) && $error['agent_tags'] === 'blank') : ?>
                            <p class="error">* 各項目一つはチェックしてください</p>
                        <?php endif; ?></td>
          </tr>
          <?php foreach ($t_list as $filter_sort) : ?>
            <tr>
              <td><?= current($filter_sort)['sort_name']; ?></td>
              <td>
                <?php foreach ($filter_sort as $filter_tag) : ?>
                  <label class="added-tag">
                    <input type="checkbox" name="agent_tags[]" value=<?= $filter_tag['tag_id'] ?> <?php if ($agent_tags) : foreach ($agent_tags as $agent_tag) :
                                                                                                      if ($filter_tag['tag_id'] === (int)$agent_tag) : ?>checked <?php endif;
                                                                                                                                                              endforeach;
                                                                                                                                                            endif; ?> />
                    <span><?= $filter_tag['tag_name']; ?></span> </label>
                <?php endforeach; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

        <div class="flex_btn">
          <a href="../detail.php?id=<?= $id ?>">エージェント詳細画面へ戻る</a>
          <input type="submit" value="編集内容を確認する" />
        </div>
      </form>
    </div>
  </main>
</body>

</html>
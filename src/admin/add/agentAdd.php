<?php
session_start();
require('../../db_connect.php');

//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
  header("Location: ../login/login.php");
  exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'rewrite' && isset($_SESSION['form'])) {
  $form = $_SESSION['form'];
  $s_filename = $form['insert_logo'];
}else{
  $form = [
    'agent_tags' => [],
  ];
}

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
    'login_email' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'login_pass' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
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
  ); // タグは配列

  $form = filter_input_array(INPUT_POST, $args);



  // エラー判定
    // タグ選択必須テスト
  if (!$form['agent_tags']) {
    $error['agent_tags'] = 'blank';
}else{
  foreach($form['agent_tags'] as $agent_tag){
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

  if ($form['started_at'] > $form['ended_at']) {
    $error['period'] = 'reverse';
  }
  // login_emailの重複チェック
  if ($form['login_email'] != '') {
    $stmt = $db->prepare('select count(*) from agents where login_email=:login_email');
    if (!$stmt) {
      die($db->error);
    }
    $stmt->bindValue('login_email', $form['login_email'], PDO::PARAM_STR);
    $success = $stmt->execute();
    $cnt = (int)$stmt->fetchColumn();
    if ($cnt > 0) {
      $error['login_email'] = 'duplicate';
    }
  }

  // 画像のチェック(変更は任意)
  $insert_logo = $_FILES['insert_logo'];
  if($insert_logo['name'] !== '' && $insert_logo['error']===0){
    $type = mime_content_type($insert_logo['tmp_name']);
    if ($type !== 'image/png' && $type !== 'image/jpeg') {
      $error['insert_logo'] = 'type';
    }
  }

  // エラーがなければ送信
  if (empty($error)) {
    $_SESSION['form'] = $form;

    if ($insert_logo['name'] !== '') {
      //画像のアップロード
      $filename = date('YmdHis') . '_' . $insert_logo['name'];
      if (!move_uploaded_file($insert_logo['tmp_name'], '../../img/insert_logo/' . $filename)) {
        die('ファイルのアップロードに失敗しました');
      }
      $_SESSION['form']['insert_logo'] = $filename;
    } else {
      $_SESSION['form']['insert_logo'] = $s_filename;
    }

    header('location: check.php');
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
            <li class="header-nav-item select">エージェント追加</li>
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
    <h1 class="main-title">エージェント追加画面</h1>
    <div class="agent-add-table">
      <form action="" method="post" enctype="multipart/form-data">
        <table class="main-info-table">
          <p><span class="error">*</span>は必須項目</p>
          <tr>
            <th>法人名<span class="error">*</span></th>
            <td><input type="text" name="corporate_name" value="<?php echo h($form["corporate_name"]); ?>" required /></td>
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
              <input type="date" name="started_at" value="<?php echo h($form["started_at"]); ?>" required /> ～
              <input type="date" name="ended_at" value="<?php echo h($form["ended_at"]); ?>" required />
              <?php if (isset($error['period']) && $error['period'] === 'reverse') : ?>
                <p class="error">* 終了日を開始日より後に設定してください。</p>
              <?php endif; ?>
            </td>
          </tr>
          <tr class="login-info">
            <th>ログイン情報<span class="error">*</span></th>
            <td>
              email:<input type="email" name="login_email" value="<?php echo h($form["login_email"]); ?>" required />　　　pass:<input type="password" name="login_pass" value="<?php echo h($form["login_pass"]); ?>" required />
              <?php if (isset($error['login_email']) && $error['login_email'] === 'duplicate') : ?>
                <p class="error">* 指定されたメールアドレスはすでに登録されています</p><?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>学生情報送信先<span class="error">*</span></th>
            <td><input type="email" name="to_send_email" value="<?php echo h($form["to_send_email"]); ?>" required />
          </tr>
          <tr>
            <th>申し込み上限数（/月）<span class="error">*</span></th>
            <td><input type="number" name="application_max" value="<?php echo h($form["application_max"]); ?>" min="1" required /> 件
          </tr>
          <tr>
            <th>請求金額（/件）<span class="error">*</span></th>
            <td><input type="number" name="charge" value="<?php echo h($form["charge"]); ?>" required /> 円
          </tr>
        </table>
        <table class="contact-info-table">
          <tr>
            <th>担当者情報</th>
          </tr>
          <tr>
            <td class="sub-th">氏名<span class="error">*</span></td>
            <td><input type="text" name="client_name" value="<?php echo h($form["client_name"]); ?>" required /></td>
          </tr>
          <tr>
            <td class="sub-th">部署名<span class="error">*</span></td>
            <td><input type="text" name="client_department" value="<?php echo h($form["client_department"]); ?>" required /></td>
          </tr>
          <tr class="contact-number">
            <td class="sub-th">連絡先<span class="error">*</span></td>
            <td>
              email:<input type="email" name="client_email" value="<?php echo h($form["client_email"]); ?>" required />　　　tel:<input type="tel" name="client_tel" value="<?php echo h($form["client_tel"]); ?>" required />
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
              <input type="text" name="insert_company_name" value="<?php echo h($form["insert_company_name"]); ?>" required />
            </td>
          </tr>
          <tr>
            <td class="sub-th">企業ロゴ<span class="error">*</span></td>
            <td><input type="file" name="insert_logo" 
            <?php if(!isset($s_filename)): ?> required <?php endif;?>/>
              <?php if (isset($error['insert_logo']) && $error['insert_logo'] === 'type') : ?>
                <p class="error">* 写真などは「.png」または「.jpg」の画像を指定してください</p>
              <?php endif; ?>
              <?php if(isset($s_filename)) : ?>
                <p class="error">* ロゴを変更する場合は写真を選択してください</p>
              <?php endif;?>
            </td>
          </tr>
          <tr>
            <td class="sub-th">オススメポイント<span class="error">*</span></td>
            <td>
              <input class="recommend_under" maxlength="40" type="text" name="insert_recommend_1" placeholder="40文字以内で入力してください" value="<?php echo h($form["insert_recommend_1"]); ?>" required /><input class="recommend_under" maxlength="40" type="text" name="insert_recommend_2" placeholder="40文字以内で入力してください" value="<?php echo h($form["insert_recommend_2"]); ?>" required /><input class="recommend_under" maxlength="40" type="text" name="insert_recommend_3" placeholder="40文字以内で入力してください" value="<?php echo h($form["insert_recommend_3"]); ?>" required />
            </td>
          </tr>
          <tr>
            <td class="sub-th">取扱い企業数<span class="error">*</span></td>
            <td><input type="text" name="insert_handled_number" value="<?php echo h($form["insert_handled_number"]); ?>" required /></td>
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
                    <input type="checkbox" name="agent_tags[]" value="<?= $filter_tag['tag_id'] ?>" <?php if ($form['agent_tags']) : foreach ($form['agent_tags'] as $agent_tag) : if (h($filter_tag['tag_id']) === $agent_tag) : ?>checked <?php endif;
                                                                                                                                                                                                                                        endforeach;
                                                                                                                                                                                                                                      endif; ?> />
                    <span><?= $filter_tag['tag_name']; ?></span> </label>
                <?php endforeach; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

        <div><input type="submit" value="入力内容を確認する"/></div>
      </form>
    </div>
  </main>
</body>

</html>
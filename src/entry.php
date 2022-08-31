<?php
session_start();
require('db_connect.php');

// ログインされていない場合は強制的にログインページにリダイレクト
// if (!isset($_SESSION["form"])) {
//     header("Location: index.php");
//     exit();
// }

if (!isset($_POST['student_contacts']) && !isset($_SESSION['form'])) {
  header('location: index.php');
  exit();
} elseif (isset($_GET['action']) && $_GET['action'] === 'rewrite') {
  $form = $_SESSION['form'];
  $student_contacts = $_SESSION['form']['student_contacts'];
  // rewriteのときcontactNULL修正
} else {
  $student_contacts = $_POST['student_contacts'];
  $form = [
    'name' => '',
    'collage' => '',
    'department' => '',
    'class_of' => '',
    'email' => '',
    'tel' => '',
    'address' => '',
    'acceptance' => '', //プライバシーポリシー
  ];
}

// var_dump($form);

$error = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $args = array(
    'name' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'collage' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'department' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'class_of' => FILTER_SANITIZE_NUMBER_INT,
    'email' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'tel' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'address' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'memo' => FILTER_SANITIZE_FULL_SPECIAL_CHARS,
    'acceptance' => FILTER_SANITIZE_NUMBER_INT,
    'student_contacts' => array(
      'filter' => FILTER_SANITIZE_NUMBER_INT,
      'flags'     => FILTER_REQUIRE_ARRAY,
    ),
  );

  $form = filter_input_array(INPUT_POST, $args);


  // エラー判定
  if ($form['name'] === '') {
    $error['name'] = 'blank';
  }
  if ($form['collage'] === '') {
    $error['collage'] = 'blank';
  }
  if ($form['department'] === '') {
    $error['department'] = 'blank';
  }
  if ($form['class_of'] === '') {
    $error['class_of'] = 'blank';
  }
  if ($form['email'] === '') {
    $error['email'] = 'blank';
  }
  if ($form['tel'] === '') {
    $error['tel'] = 'blank';
  }
  if ($form['address'] === '') {
    $error['address'] = 'blank';
  }
  if (!$form['acceptance']) { //← === ''にするとch
    $error['acceptance'] = 'blank';
  }

  // エラーがなければ送信
  if (empty($error)) {
    $_SESSION['form'] = $form;
    header('location: userCheck.php');
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
  <title>問い合わせフォーム</title>
  <link rel="stylesheet" type="text/css" href="reset.css" />
  <link rel="stylesheet" type="text/css" href="contact_style.css" />
</head>

<body>
  <main>
    <header>
      <img src="logo.png" alt="">
      <nav>
        <ul>
          <li><a href="#">就活サイト</a></li>
          <li><a href="#">就活支援サービス</a></li>
          <li><a href="#">就活の教科書とは</a></li>
          <li><a href="#">お問い合わせ</a></li>
        </ul>
      </nav>
    </header>
    <!-- フォーム -->
    <div class="box_con">
    <h1 class="inquiry_form_title">問い合わせフォーム</h1>
      <form action="" method="post" enctype="multipart/form-data">
        <?php if (isset($student_contacts)) : foreach ($student_contacts as $student_contact) : ?>
            <input type="hidden" name="student_contacts[]" value="<?= $student_contact ?>">
        <?php endforeach;
        endif; ?>
        <table class="formTable">
          <tr>
            <th>氏名<span>必須</span></th>
            <td><input size="20" type="text" name="name" value="<?php echo h($form["name"]); ?>" placeholder="例) 山田太郎"/>フルネームで記載ください<?php if (isset($error['name']) && $error['name'] === 'blank') : ?><p class="error">* 氏名は必須項目です</p>
            <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>電話番号（半角）<span>必須</span></th>
            <td><input size="30" type="tel" name="tel" value="<?php echo h($form["tel"]); ?>" placeholder="例) 09011112222"/><?php if (isset($error['tel']) && $error['tel'] === 'blank') : ?>
                <p class="error">* 電話番号は必須項目です</p>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>Email（半角）<span>必須</span></th>
            <td><input size="30" type="email" name="email" value="<?php echo h($form["email"]); ?>" placeholder="例) craft@boozer.com"/>連絡が必ずとれるEmailアドレスを記載ください。<?php if (isset($error['email']) && $error['email'] === 'blank') : ?><p class="error">* Emailアドレスは必須項目です</p>
            <?php endif; ?>
            </td>
          </tr>
          <th>学校名(大学/大学院/専門学校/短大/高校等) <span>必須</span></th>
          <td><input size="30" type="text" name="collage" value="<?php echo h($form["collage"]); ?>" placeholder="例) クラフト大学"/><?php if (isset($error['collage']) && $error['collage'] === 'blank') : ?>
              <p class="error">* 学校名は必須項目です</p>
            <?php endif; ?>
          </td>
          </tr>
          <tr>
            <th>学部/学科 <span>必須</span></th>
            <td><input size="30" type="text" name="department" value="<?php echo h($form["department"]); ?>" placeholder="例) 経済学部経済学科"/>ない方はなしと記載ください<?php if (isset($error['department']) && $error['department'] === 'blank') : ?><p class="error">* 学部/学科は必須項目です</p>
            <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>卒業年度 <span>必須</span></th>
            <td><select value="" name="class_of">
                <option hidden>選択してください</option>
                <option value="24" <?php if ($form['class_of'] === "24") : ?>selected<?php endif; ?>>24年度卒</option>
                <option value="25" <?php if ($form['class_of'] === "25") : ?>selected<?php endif; ?>>25年度卒</option>
                <option value="26" <?php if ($form['class_of'] === "26") : ?>selected<?php endif; ?>>26年度卒</option>
              </select>
              <?php if (isset($error['class_of']) && $error['class_of'] === 'blank') : ?><p class="error">* 卒業年度は必須項目です</p>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>住所 <span>必須</span></th>
            <td><input size="30" type="text" name="address" value="<?php echo h($form["address"]); ?>" placeholder="例) 東京都港区南青山３丁目１５−９ MINOWA表参道 3階"/><?php if (isset($error['address']) && $error['address'] === 'blank') : ?>
                <p class="error">* 住所は必須項目です</p>
              <?php endif; ?>
            </td>
          </tr>
          <tr>
            <th>備考欄<br /></th>
            <td><textarea name="memo" cols="50" rows="5" value="<?php echo h($form["memo"]); ?>"><?php echo h($form["memo"]); ?></textarea>エージェント企業への質問や要望があればご記入ください。</br>
              ご記入いただいた内容は選択された全てのエージェント企業にお伝えします。
            </td>
          </tr>

        </table>
        <div class="con_pri">
          <div class="box_pri">
            <div class="box_tori">
              <h4>プライバシーポリシー</h4>
              <p class="txt">当社は、当社が取得した個人情報の取扱いに関し、個人情報の保護に関する法律、個人情報保護に関するガイドライン等の指針、その他個人情報保護に関する関係法令を遵守します。</p>
            </div>
            <div class="box_num">
              <h4>２.個人情報の安全管理</h4>
              <p class="txt">当社は、個人情報の保護に関して、組織的、物理的、人的、技術的に適切な対策を実施し、当社の取り扱う個人情報の漏えい、滅失又はき損の防止その他の個人情報の安全管理のために必要かつ適切な措置を講ずるものとします。</p>
            </div>
            <div class="box_num">
              <h4>３.個人情報の取得等の遵守事項</h4>
              <p class="txt">当社による個人情報の取得、利用、提供については、以下の事項を遵守します。</p>
            </div>
            <div class="box_num">
              <h4>(1)個人情報の取得</h4>
              <p class="txt">当社は、当社が管理するインターネットによる情報提供サイト（以下「本サイト」といいます。）の運営に必要な範囲で、本サイトの一般利用者（以下「ユーザー」といいます。）又は本サイトに広告掲載を行う者（以下「掲載主」といいます。）から、ユーザー又は掲載主に係る個人情報を取得することがあります。</p>
            </div>
            <div class="box_num">
              <h4>(2)個人情報の利用目的</h4>
              <p class="txt">当社は、当社が取得した個人情報について、法令に定める場合又は本人の同意を得た場合を除き、以下に定める利用目的の達成に必要な範囲を超えて利用することはありません。
                </br>①　本サイトの運営、維持、管理
                </br>②　本サイトを通じたサービスの提供及び紹介
                </br>③　本サイトの品質向上のためのアンケート</p>
            </div>
            <div class="box_num">
              <h4>(3)個人情報の提供等</h4>
              <p class="txt">当社は、法令で定める場合を除き、本人の同意に基づき取得した個人情報を、本人の事前の同意なく第三者に提供することはありません。なお、本人の求めによる個人情報の開示、訂正、追加若しくは削除又は利用目的の通知については、法令に従いこれを行うとともに、ご意見、ご相談に関して適切に対応します。</p>
            </div>
            <div class="box_num">
              <h4>4 .個人情報の利用目的の変更</h4>
              <p class="txt">当社は、前項で特定した利用目的は、予め本人の同意を得た場合を除くほかは、原則として変更しません。但し、変更前の利用目的と相当の関連性を有すると合理的に認められる範囲において、予め変更後の利用目的を公表の上で変更を行う場合はこの限りではありません。</p>
            </div>
            <div class="box_num">
              <h4>５.個人情報の第三者提供</h4>
              <p class="txt">当社は、個人情報の取扱いの全部又は一部を第三者に委託する場合、その適格性を十分に審査し、その取扱いを委託された個人情報の安全管理が図られるよう、委託を受けた者に対する必要かつ適切な監督を行うこととします。</p>
            </div>
          </div>
        </div>
        <div class="box_check">
          <label>
            <input type="checkbox" name="acceptance" value="1" aria-invalid="false" class="agree" <?php if ($form['acceptance'] === "1") : ?>checked <?php endif; ?> /><span class="check">プライバシーポリシーに同意する</span>
          </label>
          <?php if (isset($error['acceptance']) && $error['acceptance'] === 'blank') : ?>
            <p>*ご同意いただけない場合は送信ができません。</p>
          <?php endif; ?>
        </div>
        <div class="btn">
        <button type="button"  onclick="location.href='index.php'" class="back_btn">&laquo;&nbsp;サイトに戻る</button>
            <input type="submit" id="check_form" value="確認する" />
        </div>
      </form>
    </div>
    <footer>
      <div class="inquiry">
        <p>
          craft運営 boozer株式会社事務局
          <br>TEL:080-3434-2435
          <br>Email:craft@boozer.com
        </p>
      </div>
    </footer>
    <!-- ここまで -->
  </main>
</body>

</html>
<?php

session_start();
require('db_connect.php');

//ログインされていない場合は強制的にログインページにリダイレクト
// if (!isset($_SESSION['form'])) {
//     header("Location: index.php");
//     exit();
// }

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="reset.css">
  <link rel="stylesheet" type="text/css" href="style.css">
  <title>問い合わせ完了</title>
</head>

<body>
  <!-- ヘッダー -->
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
  <div class="inquiry_done">
  <!-- <img src="logo.png" alt=""> -->
    <h1 class="green_line">問い合わせ完了</h1>
    <p>お問い合わせありがとうございます。</br>エージェント企業の担当者の方からのご連絡をお待ちください。</p>
    <p>システムによる自動返信にて、受付完了メールを送信しております。</p>
    <p>メールが届かない場合は、お手数ですが弊社までご一報ください。</p>
    <a href="index.php"> トップページに戻る </a>
  </div>

      <footer>
        <div class="inquiry bottom">
            <p>
                craft運営 boozer株式会社事務局
                <br>TEL:080-3434-2435
                <br>Email:craft@boozer.com
            </p>
        </div>
    </footer>
</body>

</html>
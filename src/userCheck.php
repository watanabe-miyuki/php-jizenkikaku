<?php
session_start();
require('db_connect.php');


if (isset($_SESSION['form']) && isset($_SESSION['form']['student_contacts'])) {
  $form = $_SESSION['form'];
  // var_dump($form);
} else {
  // var_dump($_SESSION['form']);//rewriteのときcontactNULL
  // header('location: index.php');
  // exit();
}

// agent確認用
$stmt = $db->prepare('select insert_company_name from agents where id = :id');
// var_dump($form['student_contacts']);
foreach ($form['student_contacts'] as $student_contact) :
  $stmt->bindValue('id', (int)$student_contact, PDO::PARAM_INT);
  $stmt->execute();
  $s_agents[] = $stmt->fetch(PDO::FETCH_COLUMN);
endforeach;
// var_dump($s_agents);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $stmt = $db->prepare('insert into students (name, collage, department, class_of, email, tel, address, memo) VALUES (:name, :collage, :department, :class_of, :email, :tel, :address, :memo)');
  $stmt->bindValue('name', $form['name'], PDO::PARAM_STR);
  $stmt->bindValue('collage', $form['collage'], PDO::PARAM_STR);
  $stmt->bindValue('department', $form['department'], PDO::PARAM_STR);
  $stmt->bindValue('class_of', $form['class_of'], PDO::PARAM_INT);
  $stmt->bindValue('email', $form['email'], PDO::PARAM_STR);
  $stmt->bindValue('tel', $form['tel'], PDO::PARAM_STR);
  $stmt->bindValue('address', $form['address'], PDO::PARAM_STR);
  $stmt->bindValue('memo', $form['memo'], PDO::PARAM_STR);
  if (!$stmt) {
    die($db->error);
  }
  $success = $stmt->execute();
  if (!$success) {
    die($db->error);
  }

  // students_contactsへ一斉送信
  $stmt = $db->query('select id from students where id = LAST_INSERT_ID()');
  $student_id = $stmt->fetch(PDO::FETCH_ASSOC);
  $stmt = $db->prepare("insert into students_contacts (student_id, agent_id, reason) VALUES (:student_id, :agent_id, '')");
  foreach ($form['student_contacts'] as $student_contact) :
    $stmt->bindValue('student_id', $student_id['id'], PDO::PARAM_INT);
    $stmt->bindValue('agent_id', $student_contact, PDO::PARAM_INT);
    if (!$stmt) {
      die($db->error);
    }
    $success = $stmt->execute();
    if (!$success) {
      die($db->error);
    }
  endforeach;

  // agentへ一斉送信
  // agent email
  $s_message = '';
  $stmt = $db->prepare('select * from agents where id = :id');
  // var_dump($form['student_contacts']);
  foreach ($form['student_contacts'] as $student_contact) :
    $stmt->bindValue('id', (int)$student_contact, PDO::PARAM_INT);
    $stmt->execute();
    $agent = $stmt->fetch(PDO::FETCH_ASSOC);  

    // var_dump($agents_email);
  mb_language("Japanese");
	mb_internal_encoding("UTF-8");
 
	$to = $agent['to_send_email'];
	$subject = '【boozer株式会社】学生お問い合わせのお知らせ';
  $message = "※このメールはシステムからの自動返信です
  
  ".$agent['client_name']."様
  
  お世話になっております。
  boozer株式会社でございます。
  
  以下の内容で弊社サイトからお問い合わせがありました。
  いたずらメールとご判断されましたら、お手数ですが管理サイトから通報をお願いします。確認次第、請求対象からお外しします。

  また、なにかありましたら、craft@boozer.comにお問い合わせください。なお、営業時間は平日9時〜18時となっております。
  時間外のお問い合わせは翌営業日にご連絡差し上げます。
  
  ご理解・ご了承の程よろしくお願い致します。
  
  ━━━━━━□■□　学生情報　□■□━━━━━━
  氏名：".h($form["name"])."
  電話番号：".h($form["tel"])."
  Email：".h($form["email"])."
  学校名(大学/大学院/専門学校/短大/高校等)：".h($form["collage"])."
  学部/学科：".h($form["department"])."
  卒業年度：".h($form["class_of"])."年卒
  住所：".h($form["address"])."
  備考欄：".h($form["memo"])."
  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  
  ———————————————————————
  craft運営 boozer株式会社事務局
  担当：山田　太郎
  TEL:080-3434-2435
  Email:craft@boozer.com
  
  【会社情報】
  住所：〒111-1111　東京都港区5-6-7-8
  電話番号：090-1000-2000
  営業時間：平日 9時～18時
  ———————————————————————";
  $header = ['From'=>'craft@boozer.com', 'Content-Type'=>'text/plain; charset=UTF-8', 'Content-Transfer-Encoding'=>'8bit'];
  $result = mb_send_mail($to,$subject,$message,$header);
  if(!$result){
    echo 'メールの送信に失敗しました';
  }
    // agentへ一斉送信ここまで

    // 学生送信メール用エージェント企業名配列
    $s_message .= "・".$agent['insert_company_name']."\n  ";
endforeach;
// var_dump($s_message);

  //学生問い合わせ確認メール
  if(!empty($s_message)){
  $stmt = $db->prepare('select * from agents where id = :id');

  mb_language("Japanese");
	mb_internal_encoding("UTF-8");
  $to = $form['email'];
	$subject = '【boozer株式会社】問い合わせ確認メール';
  $message = "
  ※このメールはシステムからの自動返信です
  
  ".h($form["name"])."様

  この度はCRAFTをお使いいただきありがとうございました。
  エージェント企業への問い合わせが完了しました。
  
  
  お問い合わせいただいたエージェント企業から、近日中にご連絡がありますので、今しばらくお待ちくださいませ。しばらくたっても連絡が来ない場合はお手数ですが、craft@boozer.comにお問い合わせください。なお、営業時間は平日9時〜18時となっております。
  時間外のお問い合わせは翌営業日にご連絡差し上げます。
  
  ご理解・ご了承の程よろしくお願い致します。
  
  ━━━ お問い合わせしたエージェント企業━━━━
  ".$s_message."
  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  
  ご不明な点やご質問がございましたら、
  お気軽にお問い合わせくださいませ。
  今後ともどうぞよろしくお願いいたします。
  
  ———————————————————————
  craft運営 boozer株式会社事務局
  担当：山田　太郎
  TEL:080-3434-2435
  Email:craft@boozer.com
  
  【会社情報】
  住所：〒111-1111　東京都港区5-6-7-8
  電話番号：090-1000-2000
  営業時間：平日 9時～18時
  ———————————————————————";
  $header = ['From'=>'craft@boozer.com', 'Content-Type'=>'text/plain; charset=UTF-8', 'Content-Transfer-Encoding'=>'8bit'];
  $result = mb_send_mail($to,$subject,$message,$header);
  if(!$result){
    echo 'メールの送信に失敗しました';
  }
  }
// 学生確認メールここまで

  unset($_SESSION['form']);
  header('location: thanks.php');
}


try {
  // 全てのエージェントの掲載ステータスをupdateする。
  date_default_timezone_set('Asia/Tokyo');
  $today = date("Y-m-d");

  // 掲載再開
  $stmt = $db->prepare('update agents set list_status=1 where started_at <= :started_at and ended_at >= :ended_at');
  $stmt->bindValue(':started_at', $today, PDO::PARAM_STR);
  $stmt->bindValue(':ended_at', $today, PDO::PARAM_STR);
  $success = $stmt->execute();
  if (!$success) {
      die($db->error);
  }

  // // 申し込み上限数到達(今月の申し込み数と比較)
  // // 全てのエージェントでforeach
  // // 全てのエージェント
  $stmt = $db->query('select id from agents');
  $stmt->execute();
  $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // // 今月の申し込み数
   foreach ($agents as $agent) {
       $stmt = $db->prepare('SELECT * FROM students AS S, students_contacts AS SC, agents AS A WHERE S.id = SC.student_id AND SC.agent_id = A.id AND SC.agent_id = :agent_id AND DATE_FORMAT(S.created, "%Y-%m") = :form_month ');
       $stmt->bindValue(':form_month', Date('Y-m'), PDO::PARAM_STR);
       $stmt->bindValue(':agent_id', $agent['id'], PDO::PARAM_INT);
       $stmt->execute();
       $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
       $cnt = count($result);
       // 比較
       $stmt = $db->prepare('update agents set list_status=3 where id= :id and application_max <= :application');
       $stmt->bindValue(':id', $agent['id'], PDO::PARAM_INT);
       $stmt->bindValue(':application', $cnt, PDO::PARAM_INT);
       $success = $stmt->execute();
      if (!$success) {
          die($db->error);
      }
  }

   // 掲載期間外
   $stmt = $db->prepare('update agents set list_status=2 where started_at > :started_at or ended_at < :ended_at');
   $stmt->bindValue(':started_at', $today, PDO::PARAM_STR);
   $stmt->bindValue(':ended_at', $today, PDO::PARAM_STR);
   $stmt->execute();
   $success = $stmt->execute();
   if (!$success) {
       die($db->error);
   }
   // upadateここまで

  $stmt = $db->prepare('select * from agents where list_status=?');
  $stmt->execute([1]);
  $listed_agents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  echo '接続失敗';
  $e->getMessage();
  exit();
};

//タグ情報
$stmt = $db->query('select fs.id, sort_name, tag_id, tag_name from filter_sorts fs inner join filter_tags ft on fs.id = ft.sort_id;
');
$filter_sorts_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
$t_list = [];
foreach ($filter_sorts_tags as $f) {
    $t_list[(int)$f['id']][] = $f;
}

// タグ表示テスト　htmlの上に各部分
$stmt = $db->query('select agent_id, at.tag_id, sort_id, tag_name from agents_tags at, filter_tags ft where at.tag_id = ft.tag_id');
$agents_tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
$at_list = [];

// var_dump($agents_tags[0]);
foreach ($agents_tags as $a) {
    $at_list[(int)$a['agent_id']][] = $a;
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>userEntry</title>
  <link rel="stylesheet" href="reset.css">
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="./style.css" />
  <link rel="stylesheet" type="text/css" href="contact_style.css" />
  <!-- <script src="./js/jquery-3.6.0.min.js"></script>
  <script src="./js/script.js" defer></script> -->
  <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
</head>

<body>
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
  <main>
    <div class="box_con">
    <h1 class="inquiry_form_title">確認画面</h1>
      <form method="post" action="">
      
        <table class="formTable">
          <tr>
            <th>氏名</th>
            <td><?php echo h($form["name"]); ?>
            </td>
          </tr>
          <tr>
            <th>電話番号（半角）</th>
            <td><?php echo h($form["tel"]); ?>
            </td>
          </tr>
          <tr>
            <th>Email（半角）</th>
            <td><?php echo h($form["email"]); ?>
            </td>
          </tr>
          <th>学校名(大学/大学院/専門学校/短大/高校等) </th>
          <td><?php echo h($form["collage"]); ?>
          </td>
          </tr>
          <tr>
            <th>学部/学科 </th>
            <td><?php echo h($form["department"]); ?>
            </td>
          </tr>
          <tr>
            <th>卒業年度 </th>
            <td><?php echo h($form["class_of"]); ?>年度卒
            </td>
          <tr>
            <th>住所</th>
            <td><?php echo h($form["address"]); ?>
            </td>
          </tr>
          </tr>
          <tr>
            <th>備考欄</th>
            <td><?php echo h($form["memo"]); ?>
          </tr>
          <tr>
            <th>問い合わせるエージェント企業</th>
            <td>
              <?php foreach ($s_agents as $s_agent) : ?>
                ・<?= $s_agent ?></br>
              <?php endforeach; ?>

          </tr>
        </table>
        <p class="to_btn">
        
          <a class="back_btn2" href="entry.php?action=rewrite">&laquo;&nbsp;入力画面へ戻る</a>  <span><input type="submit" value="　 送信 　" /></span>
        </p>
      </form>
    </div>
  </main>

  <footer>
        <div class="inquiry">
            <p>
                craft運営 boozer株式会社事務局
                <br>TEL:080-3434-2435
                <br>Email:craft@boozer.com
            </p>
        </div>
    </footer>
    <script src="main.js"></script>
</body>

</html>
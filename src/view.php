<?php
session_start();
require('library.php');

if (isset($_SESSION['id']) && isset($_SESSION['name'])) {
	$id = $_SESSION['id'];
	$name = $_SESSION['name'];
} else {
	header('Location: login.php');
	exit();
}

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$id) {
	header('Location: index.php');
	exit();
}

$db = dbconnect();
?>
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>

	<link rel="stylesheet" href="style.css" />
</head>

<body>
	<div id="wrap">
		<div id="head">
			<h1>ひとこと掲示板</h1>
		</div>
		<div id="content">
			<p>&laquo;<a href="index.php">一覧にもどる</a></p>
			<?php
			$stmt = $db->prepare('select p.id, p.member_id, p.message, p.created, m.name, m.picture from posts p, members m where p.id=? and m.id=p.member_id order by id desc');
			if (!$stmt) {
				die($db->error);
			}
			$stmt->bindValue(1, $id, PDO::PARAM_INT);
			$stmt->execute();
			$result = $stmt->fetch(); // データがなければ $result = false が入る
			if ($result) :
			?>
				<div class="msg">
					<?php if ($result['picture']) : ?>
						<img src="member_picture/<?php echo h($result['picture']); ?>" width="48" height="48" alt="" />
					<?php endif; ?>
					<p><?php echo h($result['message']); ?><span class="name">（<?php echo h($result['name']); ?>）</span></p>
					<p class="day"><a href="view.php?id="><?php echo h($result['created']); ?></a>
						[<a href="delete.php?id=" style="color: #F33;">削除</a>]
					</p>
				</div>
			<?php else : ?>
				<p>その投稿は削除されたか、URLが間違えています</p>
			<?php endif; ?>
		</div>
	</div>
</body>

</html>
<?php

// $value = array();
function h($value) {
	return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$dsn = 'mysql:host=db;dbname=jizen;charset=utf8mb4;';
$user = 'posse_user';
$password = 'password';


try {
  $db = new PDO($dsn, $user, $password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);//追加した！
} catch (PDOException $e) {
  echo '接続失敗: ' . $e->getMessage();
  exit();
}

?>
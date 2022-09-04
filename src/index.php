<?php
require('db_connect.php');

// ユーザー入力なし query
$sql = 'select * from members where id = 4';//sql
$stmt = $pdo->query($sql); //sql実行
$result = $stmt->PDO::FETCH_ASSOC();
echo '<pre>';
var_dump($result);
echo '</pre>';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./reset.css">
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    ハローワールドだよ
<script src="main.js"></script>
</body>
</html>
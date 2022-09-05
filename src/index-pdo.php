<?php
require('db_connect.php');

// ユーザー入力なし query
$sql = 'select * from members where id = 4';//sql
$stmt = $db->query($sql); //sql実行
$result = $stmt->fetchall();
echo '<pre>';
var_dump($result);
echo '</pre>';

$sql = 'select * from members where id = :id'; //名前付きプレースホルダ
$stmt = $db->prepare($sql);//プリペアードステートメント
$stmt->bindValue('id', 3, PDO::PARAM_INT);//紐付け
$stmt->execute(); //実行

$result = $stmt->fetchall();

echo '<pre>';
var_dump($result);
echo '</pre>';


// $stmt = $db->query('select id from agents');
// $stmt->execute();
// $agents = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
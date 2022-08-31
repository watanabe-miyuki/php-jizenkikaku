<?php 
require('../db_connect.php');

session_start();
//ログインされていない場合は強制的にログインページにリダイレクト
if (!isset($_SESSION["login"])) {
    header("Location: login/login.php");
    exit();
}

$id = $_GET['id'];
if(!$id){
  echo 'メモが正しく指定されていません';
  exit();
}
$stmt = $db->prepare('delete from agents where id = :id');
$stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
$stmt->execute();
header('location: index.php');
?>
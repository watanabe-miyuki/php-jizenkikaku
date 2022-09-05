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

$post_id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if (!$post_id) {
    header('Location: index.php');
    exit();
}

$db = dbconnect();
$stmt = $db->prepare('delete from posts where id=? and member_id=? limit 1');
$stmt->bindValue(1, $post_id, PDO::PARAM_INT);
$stmt->bindValue(2, $id, PDO::PARAM_INT);
$stmt->execute();

header('Location: index.php');
exit();
?>
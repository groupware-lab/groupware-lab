<?php
session_start();

//POST以外（直打ち）からのアクセスを排除
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: main.php');
  exit;
}

$_SESSION = [];
session_destroy();
header('Location: index.php');
exit;
?>
<?php
// 共通部品: 未ログイン(セッション不保持)の場合はログイン画面へ遷移する
// URL直打ちでのページ遷移を防ぐため
session_start();

if(!isset($_SESSION['user_id'])) {  
    header('Location: ../login/index.php');
    exit;
}
?>
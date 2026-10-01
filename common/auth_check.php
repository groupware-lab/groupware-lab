<?php
session_start();
/*---共通部品１---未ログインの場合（セッション不保持）の場合はログイン画面へ遷移*/ 
if(!isset($_SESSION['user_id'])) {  
    header('Location: ../login/index.php');
    exit;
}
?>
<?php
session_start();
/*---共通部品---未ログインの場合（セッション不保持）の場合はログイン画面へ遷移*/
// URL直打ちでページ遷移しないよう 
if(!isset($_SESSION['user_id'])) {  
    header('Location: ../login/index.php');
    exit;
}
?>
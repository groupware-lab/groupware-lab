<?php 
session_start();
if(!isset($_SESSION['user_id'])) {  //セッションを保持してなければ通さない（URL直打ちログイン対策）
    header('Location: /groupware-lab/login/index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title></title>
        </head>
        <body>
            <h1>近藤ソリューション</h1>
            
        </body>
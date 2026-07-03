<?php // dbに接続するためのphp

$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'groupware';

$connection = mysqli_connect($host, $user, $pass, $dbname);

if (!$connection) 
    {
    die("データベース接続失敗: " . mysqli_connect_error());    //このエラーメッセージはユーザーに表示されるのか検証する
    }
?>


<?php // dbに接続するためのphp

$host = 'localhost';
$user = 'your_username';
$pass = 'your_password';
$dbname = 'your_database';

$connection = mysqli_connect($host, $user, $pass, $dbname);

if (!$connection) 
    {
    die("データベース接続失敗: " . mysqli_connect_error());    //このエラーメッセージはユーザーに表示されるのか検証する
    }
?>


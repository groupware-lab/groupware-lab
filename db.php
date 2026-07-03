<?php// DB接続（共通）

$host = 'localhost';
$user = 'your_username';
$pass = 'your_password';
$dbname = 'your_database';

$mysqli = new mysqli($host, $user, $pass, $dbname);

// 接続エラー時は静かに終了（APIなので画面に出さない）
if ($mysqli->connect_error) 
    {
    exit;
    }

// 日本語対応
$mysqli->set_charset("utf8");
?>

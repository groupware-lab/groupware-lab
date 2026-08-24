<?php  // db接続（共通）
$host = "localhost";
$dbname = "your_db";
$user = "your_user";
$pass = "your_pass";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    exit("DB_ERROR");
}

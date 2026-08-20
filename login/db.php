<?php // dbに接続するためのphp。仮で倉部のPCのMYSQLに接続
$host = 'localhost';
$user = 'root';
$pass = 'root';
$dbname = 'groupware';

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
/*var_dump($dsn); 接続テスト*/
try {
    $connection = new PDO($dsn, $user, $pass);
    /*エラー落ちしたときに捕まえて、強制終了を防止*/
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("DB接続失敗：" . $e->getMessage());
    die("システムエラーが発生しました。しばらくしてから再度接続してください。"); /*ここがユーザーが見える画面*/
}
?>
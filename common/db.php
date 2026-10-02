<?php                                  // dbに接続するためのphp。仮でlocalPCのMYSQLに接続
require_once __DIR__ . '/config.php';  // ここでconfig.phpの変数を読み込む DBの設定情報を直書きしない

$dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

/*var_dump($dsn); 接続テスト用*/

try {
    $connection = new PDO($dsn, $user, $pass);
    /*エラー落ちしたときに捕まえて、強制終了を防止*/
    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("DB接続失敗：" . $e->getMessage());
    die("システムエラーが発生しました。しばらくしてから再度接続してください。"); /*ここがユーザーが見える画面*/
}

// ログイン試行時にDBにログを残す
function recordLoginAttempt($connection, $userId, $result) {
    try {
        $stmt = $connection->prepare("INSERT INTO login_history (user_id, result) VALUES (?, ?)");
        $stmt->execute([$userId, $result]);
    } catch (PDOException $e) {
        error_log("ログイン履歴の記録に失敗しました。" . $e->getmessage());
    }
}
?>
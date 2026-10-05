<!-- 共通部品--ログインした社員名を検索して画面に表示 -->
<?php
require_once __DIR__ . '/db.php';

$stmt = $connection->prepare('SELECT name FROM users WHERE id = :id');
$stmt->execute(([':id'=> $_SESSION['user_id']]));
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$employee_name = $user['name'] ??'不明';
?>
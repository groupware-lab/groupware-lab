<?php
// <!-- 共通部品--ログインした社員名を検索して画面に表示 -->
require_once __DIR__ . '/db.php';

//稟議内容登録で使うから部署名と職位もあわせて取得
$stmt = $connection->prepare('SELECT id, department, position, name FROM users WHERE id = :id');

$stmt->execute(([':id'=> $_SESSION['user_id']]));
$loginUser = $stmt->fetch(PDO::FETCH_ASSOC);
$employee_name = $loginUser['name'] ??'不明';
?>
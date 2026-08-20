<?php     // 稟議書確認画面のPHP
require_once 'db.php';
session_start();

$user_id  = $_SESSION['user_id'];
$ringi_id = $_GET['ringi_id'] ?? null;

// 稟議書取得
$stmt = $mysqli->prepare("SELECT * FROM ringi WHERE id=?");
$stmt->bind_param("i", $ringi_id);
$stmt->execute();
$ringi = $stmt->get_result()->fetch_assoc();

// 存在チェック & 権限チェック（申請者のみ確認可能）
if (!$ringi || $ringi['user_id'] != $user_id) {
    echo json_encode([
        "status"  => "error",
        "message" => "確認権限がありません"
    ]);
    exit;
}

// 確認画面用データを返す
echo json_encode([
    "status" => "success",
    "ringi"  => $ringi
]);

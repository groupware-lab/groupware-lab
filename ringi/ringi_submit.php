<?php     //稟議書提出のphp
require_once 'db.php';

$user_id     = $_SESSION['user_id'];
$ringi_id    = $_POST['ringi_id'] ?? null;
$approver_id = $_POST['approver_id'] ?? null;

$stmt = $mysqli->prepare("SELECT * FROM ringi WHERE id=?");
$stmt->bind_param("i", $ringi_id);
$stmt->execute();
$ringi = $stmt->get_result()->fetch_assoc();

if (!$ringi || $ringi['user_id'] != $user_id) {
    echo json_encode(["status"=>"error","message"=>"提出権限がありません"]);
    exit;
}

$u = $mysqli->prepare("
    UPDATE ringi SET status='submitted', current_approver_id=?, updated_at=NOW()
    WHERE id=?
");
$u->bind_param("ii", $approver_id, $ringi_id);
$u->execute();

// 履歴
$h = $mysqli->prepare("
    INSERT INTO ringi_history (ringi_id, action, user_id, created_at)
    VALUES (?, 'submit', ?, NOW())
");
$h->bind_param("ii", $ringi_id, $user_id);
$h->execute();

echo json_encode(["status"=>"success"]);

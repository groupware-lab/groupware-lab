<?php  //稟議書却下のphp
require_once 'db.php';
session_start();

$user_id  = $_SESSION['user_id'];
$ringi_id = $_POST['ringi_id'];

$stmt = $mysqli->prepare("
    UPDATE ringi SET
        status='rejected',
        current_approver_id=NULL,
        updated_at=NOW()
    WHERE id=? AND current_approver_id=?
");
$stmt->bind_param("ii", $ringi_id, $user_id);
$stmt->execute();

// 履歴
$h = $mysqli->prepare("
    INSERT INTO ringi_history (ringi_id, action, user_id, created_at)
    VALUES (?, 'reject', ?, NOW())
");
$h->bind_param("ii", $ringi_id, $user_id);
$h->execute();

echo json_encode(["status"=>"success"]);

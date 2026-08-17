<?php  //稟議書一覧のphp
require_once 'db.php';
session_start();

$user_id = $_SESSION['user_id'];

$stmt = $mysqli->prepare("
    SELECT * FROM ringi
    WHERE user_id=? OR current_approver_id=?
    ORDER BY updated_at DESC
");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    "status" => "success",
    "list"   => $list
]);

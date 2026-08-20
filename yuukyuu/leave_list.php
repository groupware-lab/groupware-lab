<?php  //有給申請一覧のためのphp
require_once 'login_check.php';
require_once 'db.php';

$sql = "SELECT * FROM leave_requests WHERE user_id=? ORDER BY created_at DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
?>

<?php  //有給申請履歴のためのphp
require_once 'login_check.php';
require_once 'db.php';

$sql = "SELECT id, start_date, end_date, status, created_at
        FROM leave_requests
        WHERE user_id=?
        ORDER BY created_at DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_all(MYSQLI_ASSOC));
?>

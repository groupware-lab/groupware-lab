<?php  //有給申請の詳細のためのphp
require_once 'login_check.php';
require_once 'db.php';

$id = $_GET['id'] ?? null;
//URL の ?id=〇〇 を受け取る

$sql = "SELECT * FROM leave_requests WHERE id=?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());
?>

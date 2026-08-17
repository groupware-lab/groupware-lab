<?php  //稟議書詳細のphp
require_once 'db.php';
session_start();

$user_id  = $_SESSION['user_id'];
$ringi_id = $_GET['ringi_id'];

$stmt = $mysqli->prepare("SELECT * FROM ringi WHERE id=?");
$stmt->bind_param("i", $ringi_id);
$stmt->execute();
$ringi = $stmt->get_result()->fetch_assoc();

$is_creator  = ($ringi['user_id'] == $user_id);
$is_approver = ($ringi['current_approver_id'] == $user_id);

echo json_encode([
    "status"      => "success",
    "ringi"       => $ringi,
    "is_creator"  => $is_creator,
    "is_approver" => $is_approver
]);


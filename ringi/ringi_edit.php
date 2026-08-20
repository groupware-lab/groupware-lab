<?php     //編集画面のphp
require_once 'db.php';
session_start();

$user_id  = $_SESSION['user_id'];
$ringi_id = $_GET['ringi_id'];

$stmt = $mysqli->prepare("SELECT * FROM ringi WHERE id=?");
$stmt->bind_param("i", $ringi_id);
$stmt->execute();
$ringi = $stmt->get_result()->fetch_assoc();

if (!$ringi || $ringi['user_id'] != $user_id) {
    echo json_encode(["status"=>"error","message"=>"編集権限がありません"]);
    exit;
}

echo json_encode([
    "status" => "success",
    "ringi"  => $ringi
]);

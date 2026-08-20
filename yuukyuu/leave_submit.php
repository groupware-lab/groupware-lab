<?php  //有給申請提出のためのphp
require_once 'login_check.php';
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

$sql = "UPDATE leave_requests
        SET status='submitted'
        WHERE id=? AND user_id=? AND status='draft'";
        //draft → submitted に変更
        //draft（下書き）提出→submitted（承認待ち）承認→approved（承認済み）

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

echo json_encode(["message" => "提出しました"]);
?>

<?php  //有給申請承認のためのphp
require_once 'login_check.php';
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

$sql = "UPDATE leave_requests
        SET status='approved'
        WHERE id=? AND status='submitted'";
        //submitted → approved に変更
        //draft（下書き）提出→submitted（承認待ち）承認→approved（承認済み）

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
// sql準備
// 値を入れる
// sql実行

echo json_encode(["message" => "承認しました"]);
?>

<?php  //有給申請更新のためのphp
require_once 'login_check.php';
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$start = $data['start_date'] ?? null;
$end   = $data['end_date'] ?? null;
$reason = $data['reason'] ?? null;

if (!$id) {
    echo json_encode(["error" => "id が必要です"]);
    exit;
}

$sql = "UPDATE leave_requests
        SET start_date=?, end_date=?, reason=?
        WHERE id=? AND user_id=? AND status='draft'";
        //draft 状態の申請だけ更新できるようにする
        //draft（下書き）

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sssii", $start, $end, $reason, $id, $user_id);
$stmt->execute();

echo json_encode(["message" => "更新しました"]);
?>

<?php  //有給申請削除のためのphp(キャンセル)
require_once 'login_check.php';
require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

$sql = "DELETE FROM leave_requests
        WHERE id=? AND user_id=? AND status='draft'";
        //draft 状態の申請だけ削除（キャンセル）できる
        //draft（下書き)

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();

echo json_encode(["message" => "削除しました"]);
?>

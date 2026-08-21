<?php   //有給申請作成のためのphp
require_once 'login_check.php';
require_once 'db.php';           //ログインチェックと DB 接続読み込み

$data = json_decode(file_get_contents("php://input"), true);  //フロントから送られた JSON を連想配列として受け取る
                                                              //連想配列=キー（名前）と値のセットでデータを管理する配列

$start = $data['start_date'] ?? null;
$end   = $data['end_date'] ?? null;
$reason = $data['reason'] ?? null;   //jsonから値を取り出す

if (!$start || !$end)   //必須項目チェック。どちらかが空ならエラー
    {
    echo json_encode(["error" => "start_date と end_date は必須です"]);
    exit;
    } 

$sql = "INSERT INTO leave_requests (user_id, start_date, end_date, reason)
        VALUES (?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("isss", $user_id, $start, $end, $reason);
$stmt->execute();

echo json_encode(["message" => "有給申請を作成しました", "id" => $stmt->insert_id]);
?>

<?php  //稟議書承認のphp
require_once 'db.php';
session_start();

$user_id  = $_SESSION['user_id'];
$ringi_id = $_POST['ringi_id'];

$stmt = $mysqli->prepare("
    UPDATE ringi SET
        status='approved',
        current_approver_id=NULL,
        updated_at=NOW()
    WHERE id=? AND current_approver_id=?
");         //承認者が承認ボタンを押した時のみ、稟議書を承認に更新する
$stmt->bind_param("ii", $ringi_id, $user_id);
$stmt->execute();

// 履歴
$h = $mysqli->prepare("
    INSERT INTO ringi_history (ringi_id, action, user_id, created_at)
    VALUES (?, 'approve', ?, NOW())
");     //誰がいつ承認したかをringi_historyテーブルに記録する
$h->bind_param("ii", $ringi_id, $user_id);
$h->execute();

echo json_encode(["status"=>"success"]);

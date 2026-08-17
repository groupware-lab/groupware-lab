<?php  //稟議書編集処理のphp
require_once 'db.php';
session_start();

$user_id  = $_SESSION['user_id'];
$ringi_id = $_POST['ringi_id'];

$stmt = $mysqli->prepare("SELECT * FROM ringi WHERE id=?");
$stmt->bind_param("i", $ringi_id);
$stmt->execute();
$ringi = $stmt->get_result()->fetch_assoc();

if (!$ringi || $ringi['user_id'] != $user_id) {
    echo json_encode(["status"=>"error","message"=>"編集権限がありません"]);
    exit;
}

$headline         = $_POST['headline'];
$apply_date       = $_POST['apply_date'];
$project_name     = $_POST['project_name'];
$department       = $_POST['department'];
$position         = $_POST['position'];
$applicant_name   = $_POST['applicant_name'];
$cost_estimate    = $_POST['cost_estimate'];
$desired_date     = $_POST['desired_date'];
$member_count     = $_POST['member_count'];
$outsourcing      = $_POST['outsourcing'];
$outsourcing_name = $_POST['outsourcing_name'];
$description      = $_POST['description'];

$u = $mysqli->prepare("
    UPDATE ringi SET
        headline=?, apply_date=?, project_name=?, department=?, position=?,
        applicant_name=?, cost_estimate=?, desired_date=?, member_count=?,
        outsourcing=?, outsourcing_name=?, description=?, updated_at=NOW()
    WHERE id=?
");

$u->bind_param(
    "ssssssssssssi",
    $headline, $apply_date, $project_name, $department, $position,
    $applicant_name, $cost_estimate, $desired_date, $member_count,
    $outsourcing, $outsourcing_name, $description, $ringi_id
);

$u->execute();

// 履歴
$h = $mysqli->prepare("
    INSERT INTO ringi_history (ringi_id, action, user_id, created_at)
    VALUES (?, 'update', ?, NOW())
");
$h->bind_param("ii", $ringi_id, $user_id);
$h->execute();

header("Location: ringi_confirm.php?ringi_id=" . $ringi_id);
exit;


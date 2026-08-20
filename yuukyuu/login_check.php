<?php    //有給申請のためのログインチェック
session_start();

if (!isset($_SESSION['user_id'])) {    //確認
    echo json_encode(["error" => "ログインが必要です"]);
    exit;
}

$user_id = $_SESSION['user_id'];
?>

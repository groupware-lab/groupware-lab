<?php // DB接続（共通）
$mysqli = new mysqli("localhost", "root", "password", "workflow");

if ($mysqli->connect_errno) {
    echo json_encode(["error" => "DB接続に失敗しました"]);
    exit;
}

$mysqli->set_charset("utf8mb4"); //文字コードは UTF-8（utf8mb4）を使う
?> 

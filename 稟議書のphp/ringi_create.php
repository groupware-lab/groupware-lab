<?php   //稟議書を作成するためのphp
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) 
    {
    exit(json_encode(["status"=>"error","message"=>"ログインしてください"])); //json_encode=APIの返り値をJSON形式で返す
    }

$user_id = $_SESSION['user_id'];
$title   = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';
$amount  = $_POST['amount'] ?? null;

if ($title === '' || $content === '') 
    {
    exit(json_encode(["status"=>"error","message"=>"タイトルと内容は必須です"]));
    }

$sql = "INSERT INTO ringi (user_id, title, content, amount, status)
        VALUES (?, ?, ?, ?, 'draft')";
$stmt = $mysqli->prepare($sql);  //sqlインジェクション対策
$stmt->bind_param("issi", $user_id, $title, $content, $amount);   //値を入れる   issi=i=integer（数値）s=string（文字列）
$stmt->execute();   //実行する

$ringi_id = $stmt->insert_id;  //insertしたレコードのIDを取得する

// 履歴追加
$hsql = "INSERT INTO ringi_history (ringi_id, action, user_id)
         VALUES (?, 'create', ?)";
$hstmt = $mysqli->prepare($hsql);   //prepare=sqlを使う宣言
$hstmt->bind_param("ii", $ringi_id, $user_id);  //データを入れる
$hstmt->execute();   //実行する

echo json_encode(["status"=>"success","ringi_id"=>$ringi_id]);   //PHPの配列をJSONに変換する   APIの返り値はJSONが基本
?>

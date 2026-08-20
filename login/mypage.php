<?php  //ログイン情報をフロントエンドに返すためのphp
session_start();
require_once 'db.php';

// ログインしているか確認
if (!isset($_SESSION['user_id']))
    {
    return; // ログインしていない → 何も返さず終了
    }

$user_id = $_SESSION['user_id'];

// SQL実行（ログイン中ユーザーの情報を取得）
$sql = "SELECT employee_code FROM users WHERE employee_code = $user_id";
$result = mysqli_query($connection, $sql);

// SQLエラー
if (!$result) 
    {
    return;
    }

$user = mysqli_fetch_assoc($result);

// ユーザー情報を返す（JSON形式）
header('Content-Type: application/json');
echo json_encode($user);  //json=データをやり取りするための、世界で一番使われているデータ形式

return;
?>

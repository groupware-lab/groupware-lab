<?php //ログイン機能のためのphp
session_start();
require_once 'db.php';

//フロントエンドから送られてきた値を受け入れるための箱
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// (空チェック)　不正なリクエストを防ぎ、次の処理に繋げない為
if ($email === '' || $password === '')
    {
    return; // 何も返さず終了     //エラーメッセージ表示させる
    }

// SQLインジェクション対策
$email_escaped = mysqli_real_escape_string($connection, $email);

// SQL実行
$sql = "SELECT * FROM users WHERE email = '$email_escaped'";
$result = mysqli_query($connection, $sql);

// SQLエラー
if (!$result)
   {
    return; // 何も返さず終了
   }

$user = mysqli_fetch_assoc($result);

// パスワード照合
if ($user && password_verify($password, $user['password_hash'])) 
   {
   $_SESSION['user_id'] = $user['id'];
   return; // 成功として終了
   }

// ログイン失敗
return;
?>
<?php //新規ユーザーをデータベースに登録するためのphp
session_start();
require_once 'db.php';

//フロントエンドから送られてきた値を受け入れるための箱
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// (空チェック)　不正なリクエストを防ぎ、次の処理に繋げない為
if ($email === '' || $password === '')
    {
    return; // 何も返さず終了
    }

// SQLインジェクション対策
$email_escaped = mysqli_real_escape_string($connection, $email);

// パスワードをハッシュ化
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// すでに同じメールが存在するかチェック
$sql_check = "SELECT id FROM users WHERE email = '$email_escaped'";
$result_check = mysqli_query($connection, $sql_check);

if (!$result_check)
    {
    return;
    }

$existing_user = mysqli_fetch_assoc($result_check);

if ($existing_user)
    {
    return; // すでに登録済み
    }

// 新規登録
$sql_insert = "
    INSERT INTO users (email, password_hash)
    VALUES ('$email_escaped', '$password_hash')
";

$result_insert = mysqli_query($connection, $sql_insert);

if (!$result_insert) 
    {
    return;
    }

// 登録成功 → セッション作成
$_SESSION['user_id'] = mysqli_insert_id($connection);

return;
?>
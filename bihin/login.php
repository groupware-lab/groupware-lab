<?php  //備品購入ログインのためのphp
session_start();
require_once("db.php");

$email = $_POST['email'] ?? "";
$password = $_POST['password'] ?? "";

$sql = "SELECT id, password FROM users WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(":email", $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "login_failed";
    exit;
}

if (!password_verify($password, $user['password'])) {
    echo "login_failed";
    exit;
}

$_SESSION['user_id'] = $user['id'];
echo "login_success";

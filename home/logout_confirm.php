<?php 
session_start();

if(!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ログアウト確認</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets/css/common.css" rel="stylesheet">
</head>

<body class="logout-body">
  <div class="logout-box">
    <h2>ログアウトしますか?</h2>
    <p class="text-muted small mb-4">保存していない入力内容は破棄されます。</p>
    <form action="logout.php" method="post" class="d-flex justify-content-center gap-3">
      <button type="submit" class="btn btn-success rounded-0 px-4">はい</button>
      <a href="main.php" class="btn btn-outline-dark rounded-0 px-4">いいえ</a>
    </form>
  </div>
</body>
</html>

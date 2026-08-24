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
    <meta chrset="UTF-8">
    <title></title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>
    <div class="container text-center mt-5">
      <h2>ログアウトしますか？</h2>
      <form action="logout.php" method="post" class="mat-4">
        <button type="submit" class="btn btn-secondary mx-2">はい</button>
        <a href="main.php" class="btn btn-secondary mx-2">いいえ</a>
      </form>
    </div>
  </body>
</html>

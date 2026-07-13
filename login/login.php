<?php //ログイン機能のためのphp
session_start();  //セッションIDを生成
require_once 'db.php';

//フロントエンドから送られてきた値を受け入れるための箱
$employee_id = $_POST['employee_id'] ?? '';
$password = $_POST['password'] ?? '';

// (空チェック)　
if ($employee_id === '' || $password === '')
   {
      header('Location: index.php?error=empty');
      exit;
     //エラーメッセージ(未入力です)表示させる
   }

// SQL実行
try {
   $stmt = $connection->prepare("SELECT * FROM users WHERE employee_code = ?");
   $stmt->execute([$employee_id]);
   $user = $stmt->fetch(PDO::FETCH_ASSOC);
}  catch (PDOException $e) {
   error_log("SQL実行時エラー:" . $e->getMessage());
   die("システムエラーが発生しました。お手数ですが再度お試しください。");
}

//パスワード照合
if($user && password_verify($password, $user['password_hash'])) {
   $_SESSION['user_id'] = $user['id'];
   header('Location: ../home/main.php');       //home画面に遷移させる
   exit();
}
header('Location: index.php?error=invalid');  //エラーメッセージ(社員番号またはパスワードの不一致)を表示する
exit;
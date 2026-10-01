<?php
$password = '';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;

?>

<!-- パスワードのハッシュ化のテスト用のphp
使うときはpasswordの変数の中身を自分で決めて入力して、デバックをする 
コンソールにハッシュ化された文字列が表示される -->

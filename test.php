<?php
$password = 'test1234';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo $hash;

?>
//パスワードのハッシュ化のテスト用のphp
//使うときはpasswordの変数の値を変えてデバックをする

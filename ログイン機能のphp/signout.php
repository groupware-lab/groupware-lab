<?php //今ログインしているユーザーのセッションデータを消してログアウト状態にさせるためのphp
session_start();

// セッションの中身を空にする
$_SESSION = []; //$_SESSION = [];はsession_start();を使うと使えるようになるphpの変数

// セッションを完全に破棄
session_destroy(); //phpに搭載されているセッション削除用の関数

return; // 何も返さず終了
?>
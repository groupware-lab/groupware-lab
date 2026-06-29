<?php //ログインできているのかを確認するphp //これがないと不正アクセスされる
session_start();

// ログインしているか確認
if (isset($_SESSION['user_id'])) //isset=「その変数が存在していて、かつ null じゃないか？」を調べる関数
    {
    return; // ログインしている（成功として終了）
    }

// ログインしていない
return;
?> 
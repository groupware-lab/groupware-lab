<?php 
session_start();
if(!isset($_SESSION['user_id'])) {  //セッションを保持してなければ通さない（URL直打ちログイン対策）
    header('Location: ../login/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>My Web Page</title>
    </head>

    <header>
        <div class="logo">株式会社コンドーソリューションズわほー<span></span></div>
        <nav>
        <ul>
            <li><a href="logout_confirm.php">ログアウト</a></li>
            <li><a href="#">デスクトップ</a></li>
            <li><a href="#">社内掲示板</a></li>
            <li><a href="#">社内稟議書</a></li>
            <li><a href="#">出退勤管理</a></li>
            <li><a href="#">有給申請</a></li>
            <li><a href="#">備品購入申請</a></li>
        </ul>
    </nav>
    </header>

        <main>
            <section class="card">
                <div class="card-header">社内掲示板</div>
                <div class="card-body"></div>
            </section>

            <div class="container text-center">
                <div class="row">
                　<div class="col">
                    <section class="card">
                        <div class="card-header">社内稟議書</div>
                            <div class="card-body"></div>
                    </section>
                    <section class="card">
                        <div class="card-header">出退勤管理</div>
                        <div class="card-body"></div>
                    </section>
                　</div>

                　<div class="col">
                    <section class="card">
                        <div class="card-header">有給申請</div>
                        <div class="card-body"></div>
                    </section>
                    <section class="card">
                        <div class="card-header">備品購入申請</div>
                        <div class="card-body"></div>
                    </section>
                　</div>
                </div>
            </div>
        </main>
    </body>
</html>
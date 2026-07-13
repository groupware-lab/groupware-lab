<?php 
session_start();
if(!isset($_SESSION['user_id'])) {  //セッションを保持してなければ通さない（URL直打ちログイン対策）
    header('Location: /groupware-lab/login/index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FACE OFFICE 風 ダッシュボード</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="header">
        <div class="logo">株式会社コンドーソリューションズわほー<span></span></div>
    </header>

    <nav class="main-nav">
        <a href="#" class="active">デスクトップ</a>
        <a href="#">出退勤管理</a>
        <a href="#">社内掲示板</a>
        <a href="#">社内稟議書</a>
        <a href="#">有給申請</a>
        <a href="#">備品購入申請</a>
    </nav>

    <div class="container">

        <main class="main-content">
            <div class="content-header">
                <span>デスクトップ</span>
            </div>

            <section class="card schedule-card">
                <div class="card-header">
                    <span class="card-title">社内掲示板</span>
                </div>
            </section>

            <div class="dashboard-grid">
                
                <div class="column">
                    <section class="card">
                        <div class="card-header blue-header">
                            <span class="card-title">出退勤管理</span>
                        </div>
                    </section>

                    <section class="card">
                        <div class="card-header">
                            <span class="card-title">有給申請</span>
                        </div>
                    </section>

                </div>

                <div class="column">
                    <section class="card">
                        <div class="card-header">
                            <span class="card-title">社内稟議書</span>
                        </div>
                    </section>

                    <section class="card">
                        <div class="card-header">
                            <span class="card-title">購入申請</span>
                        </div>
                    </section>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
<?php 
session_start();
<<<<<<< HEAD
if(!isset($_SESSION['user_id'])) {  
    header('Location: ..//login/index.php');
=======
if(!isset($_SESSION['user_id'])) {  //セッションを保持してなければ通さない（URL直打ちログイン対策）
    header('Location: ../login/index.php');
>>>>>>> 7880ba2041fdf0980a70f5635a3af2dab036306d
    exit;
} 
?>
<!DOCTYPE html>
<html lang="ja">
<<<<<<< HEAD
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Web Page</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="p-3">

    <header class="mb-4">
        <!-- タイトル -->
        <div class="fw-bold fs-5 mb-3">株式会社コンドウソリューションズ</div>
        
        <nav class="border border-dark p-2">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link text-dark p-0 pe-3" href="#">デスクトップ</a></li>
                <li class="nav-item"><a class="nav-link text-dark p-0 pe-3" href="#">出退勤管理</a></li>
                <li class="nav-item"><a class="nav-link text-dark p-0 pe-3" href="#">社内掲示板</a></li>
                <li class="nav-item"><a class="nav-link text-dark p-0 pe-3" href="#">社内稟議書</a></li>
                <li class="nav-item"><a class="nav-link text-dark p-0 pe-3" href="#">有給申請</a></li>
                <li class="nav-item"><a class="nav-link text-dark p-0 pe-3" href="#">備品購入申請</a></li>
            </ul>
        </nav>
=======
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
>>>>>>> 7880ba2041fdf0980a70f5635a3af2dab036306d
    </header>

    <main>
        <section class="card border-dark rounded-0 mb-3">
            <div class="card-header bg-white border-dark rounded-0 fw-bold">出退勤管理</div>
            <div class="card-body py-5"></div>
        </section>

        <div class="container-fluid p-0">
            <div class="row g-3">
                <div class="col-md-6">
                    <section class="card border-dark rounded-0 mb-3">
                        <div class="card-header bg-white border-dark rounded-0 fw-bold">社内稟議書</div>
                        <div class="card-body py-5"></div>
                    </section>
                    <section class="card border-dark rounded-0 mb-3">
                        <div class="card-header bg-white border-dark rounded-0 fw-bold">有給申請</div>
                        <div class="card-body py-5"></div>
                    </section>
                </div>

                <div class="col-md-6">
                    <section class="card border-dark rounded-0 mb-3">
                        <div class="card-header bg-white border-dark rounded-0 fw-bold">出退勤管理</div>
                        <div class="card-body py-5"></div>
                    </section>
                    <section class="card border-dark rounded-0 mb-3">
                        <div class="card-header bg-white border-dark rounded-0 fw-bold">備品購入申請</div>
                        <div class="card-body py-5"></div>
                    </section>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
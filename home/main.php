<?php 
session_start();
if(!isset($_SESSION['user_id'])) {  
    header('Location: ..//login/index.php');
    exit;
} 
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Web Page</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>

<body class="p-3">
    <header class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center">
                <span class="fw-bold fs-5">株式会社コンドウソリューションズ</span>
            </div>

            <a href="logout_confirm.php" class="text-dark text-decoration-none d-flex align-items-center" title="ログアウト">
                <img src="../assets/img/logout_icon.png" alt="ログアウト" width="20" height="20" class="me-1">
            <span class="small">ログアウト</span>
        </a>
        </div>
        
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
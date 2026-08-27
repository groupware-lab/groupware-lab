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
    <!-- assets内のBootstrap CSSを読み込み -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>

<body style="background-color: mediumaquamarine;" class="p-4">

    
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
        <!-- タイトル
        <div class="fw-bold fs-5 mb-3">株式会社コンドウソリューションズ</div> -->

        <!-- ナビゲーションメニュー（枠線・内側の余白はBootstrapクラス） -->
    <nav class="navbar navbar-expand-lg navbar-light bg-success rounded-3 shadow-sm px-3">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link text-white fw-medium" href="#">デスクトップ</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-medium" href="#">出退勤管理</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-medium" href="#">社内掲示板</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-medium" href="#">社内稟議書</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-medium" href="#">有給申請</a></li>
                <li class="nav-item"><a class="nav-link text-white fw-medium" href="#">備品購入申請</a></li>
            </ul>
        </nav>
    </header>

    <main class="card border-0 shadow-sm rounded-3 p-4 bg-white">
        <!-- 最上部のカード -->
        <section class="card border bg-light rounded-3 mb-4">
            <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 fw-bold text-dark fs-5">社内掲示板</div>
            <div class="card-body py-5"></div>
        </section>

        <!-- 2列レイアウト -->
        <div class="container-fluid p-0">
            <div class="row g-4">
                <!-- 左列 -->
                <div class="col-md-6">
                    <section class="card border bg-light rounded-3 mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 fw-bold text-dark fs-5">社内稟議書</div>
                        <div class="card-body py-5"></div>
                    </section>
                    <section class="card border bg-light rounded-3 mb-0">
                        <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 fw-bold text-dark fs-5">有給申請</div>
                        <div class="card-body py-5"></div>
                    </section>
                </div>

                <!-- 右列 -->
                <div class="col-md-6">
                    <section class="card border bg-light rounded-3 mb-4">
                        <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 fw-bold text-dark fs-5">出退勤管理</div>
                        <div class="card-body py-5"></div>
                    </section>
                    <section class="card border bg-light rounded-3 mb-0">
                        <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 fw-bold text-dark fs-5">備品購入申請</div>
                        <div class="card-body py-5"></div>
                    </section>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
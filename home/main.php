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
        <title>株式会社コンドウソリューション</title>
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="main.css">
    </head>

    <body class="bg-light d-flex flex-column min-vh-100">
        <header class="bg-main-green text-white py-3 px-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <span class="fw-bold fs-5">株式会社コンドウソリューションズ</span>
                </div>

                <a href="logout_confirm.php" class="text-white text-decoration-none d-flex align-items-center logout-link" title="ログアウト">
                    <img src="../assets/img/logout_icon.png" alt="ログアウト" width="30" height="35" class="me-1 logout-icon">
                    <span class="small">ログアウト</span>
                </a>
            </div>
        </header>

        <div class="bg-white border-bottom py-2 px-4 mb-4">
            <div class="container-fluid p-0 d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm">デスクトップ</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">出退勤管理</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">社内掲示板</button>
                <button type="button" class="btn btn-outline-secondary btn-sm">ワークフロー</button>
            </div>
        </div>

        <main class="container-fluid px-4 flex-grow-1">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border border-1 shadow-sm p-3">
                        <h2 class="h6 fw-bold mb-0">社内掲示板</h2>
                        <div style="min-height: 100px;"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border border-1 shadow-sm p-3">
                        <h2 class="h6 fw-bold mb-0">出退勤管理</h2>
                        <div style="min-height: 100px;"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border border-1 shadow-sm p-3">
                        <h2 class="h6 fw-bold mb-0">ワークフロー</h2>
                        <div style="min-height: 100px;"></div>
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-main-green text-white text-center py-3 mt-4">
            <div class="container-fluid">
                <small>&copy; KONDO SOLUTIONS Co, Ltd. All Rights Reserved.</small>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
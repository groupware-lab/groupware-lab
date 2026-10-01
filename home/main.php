<?php
require_once __DIR__ . '/../common/auth_check.php';      
require_once __DIR__ . '/../common/get_user_info.php';
?>

<!DOCTYPE html>
    <html lang="ja">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>株式会社コンドウソリューション</title>

        <!-- bootstrapのcssを読み込む用 -->
        <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

        <!-- headerとログアウトボタンの共通部品のcssを読み込む用 -->
        <link href="../assets/css/common.css" rel="stylesheet">

    </head>

    <body class="bg-light d-flex flex-column min-vh-100">
        <header class="app-header">
            <div class="app-header-inner">
                <span class="app-logo-badge">KS</span>
                <span class="app-header-title">株式会社コンドウソリューション</span>
            </div>

            <nav class="app-nav-menu d-flex align-items-center gap-4">
                <span class="small text-white">
                    こんにちは！<?php echo htmlspecialchars($employee_name, ENT_QUOTES, 'UTF-8')?>  さん
                </span>

                <span class="app-user-badge">
                    <img src="../assets/img/user_icon.png" alt="社員アイコン" width="30" height="30">
                </span>

                <a href="logout_confirm.php" class="text-white text-decoration-none d-flex align-items-center logout-link" title="ログアウト">
                    <img src="../assets/img/logout_icon.png" alt="ログアウト" width="30" height="35" class="me-1 logout-icon">
                    <span class="small">ログアウト</span>
                </a>
            </nav>
        </header>

        <div class="bg-white border-bottom py-2 px-4 mb-4">
            <div class="container-fluid p-0">
                <ul class="d-flex flex-wrap gap-2 list-unstyled mb-0 ps-0">
                    <li><a href="#" class="btn btn-outline-secondary btn-sm">デスクトップ</a></li>
                    <li><a href="#" class="btn btn-outline-secondary btn-sm">出退勤管理</a></li>
                    <li><a href="#" class="btn btn-outline-secondary btn-sm">社内掲示板</a></li>
                    <li><a href="../workflow/workflow_main.php" class="btn btn-outline-secondary btn-sm">ワークフロー</a></li>
                </ul>
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
    </body>
</html>
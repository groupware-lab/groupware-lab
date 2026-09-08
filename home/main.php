<?php
/*session_start();
if(!isset($_SESSION['user_id'])) {  
    header('Location: ..//login/index.php');
    exit;
}*/ 
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

            <nav class="app-nav-menu">
                <a href="logout_confirm.php" class="text-white text-decoration-none d-flex align-items-center logout-link" title="ログアウト">
                    <img src="../assets/img/logout_icon.png" alt="ログアウト" width="30" height="35" class="me-1 logout-icon">
                    <span class="small">ログアウト</span>
                </a>
            </nav>
        </header>

        <!-- ここから後で再度コードを確認する（倉部） -->
        <!-- ここはbuttonでない。liで明示してあげてaタグでリンク遷移するからたぶん要修正 -->
        <!-- デスクトップ以外はリンクでページ遷移するため -->
        <div class="bg-white border-bottom py-2 px-4 mb-4">
            <div class="container-fluid p-0 d-flex flex-wrap gap-2">
                <ul>
                    <li><a href="#" class="btn btn-outline-secondary btn-sm">デスクトップ</a></li>
                    <li><a href="#" class="btn btn-outline-secondary btn-sm">出退勤管理</a></li>
                    <li><a href="#" class="btn btn-outline-secondary btn-sm">社内掲示板</a></li>
                    <li><a href="#" class="btn btn-outline-secondary btn-sm">ワークフロー</a></li>
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
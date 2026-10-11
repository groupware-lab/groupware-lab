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
        <link href="../common/common.css" rel="stylesheet">

    </head>

    <body class="bg-light d-flex flex-column min-vh-100">
        <?php require_once __DIR__ . '/../common/header.php'; ?>
        
        <main class="container-fluid px-4 flex-grow-1">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border border-1 shadow-sm p-3">
                        <h2 class="h6 fw-bold mb-0">社内掲示板</h2>
                        <div style="min-height: 200px;"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border border-1 shadow-sm p-3">
                        <h2 class="h6 fw-bold mb-0">出退勤管理</h2>
                        <div style="min-height: 180px;"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border border-1 shadow-sm p-3">
                        <h2 class="h6 fw-bold mb-0">ワークフロー</h2>
                        <div style="min-height: 180px;"></div>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>
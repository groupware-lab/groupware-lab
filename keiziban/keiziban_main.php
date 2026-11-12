<?php
require_once __DIR__ . '/../common/auth_check.php';      //セッションチェック
require_once __DIR__ . '/../common/get_user_info.php';   //loginユーザー表示
require_once __DIR__ . '/../common/approver.php';        //承認ルートの共通部品
require_once __DIR__ . '/../common/db.php';              //DB接続

////申請中の稟議書の数をカウントして表示
$ringiCounter = 0;

$sql = "SELECT COUNT(*) FROM ringi
        WHERE applicant_id = :applicant_id
        AND status = 'pending'";

$stmt = $connection->prepare($sql);
$stmt->bindValue(':applicant_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$ringiCounter = (int)$stmt->fetchColumn();

$isApprover = canApprove($loginUser['position'] ?? null, 'ringi');
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

<body class="app-page d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../common/header.php'; ?>

    <main class="container-fluid px-4 flex-grow-1">
        <div class="row g-3">
            <!-- 社内掲示板 -->
            <div class="col-12">

                <div class="card border border-1 shadow-sm p-4">

                    <div class="d-flex align-items-center mb-3">
                        <h2 class="h6 fw-bold mb-0">社内掲示板</h2>

                            <!-- ★ ms-auto で右端に押し出す ★ -->
                            <div class="d-flex align-items-center gap-2 ms-auto">
                                <a href="keiziban_sinsei.php" class="btn btn-primary btn-sm">
                                +新規作成
                                </a>
                                    <label for="boardCategory" class="form-label small mb-0 text-nowrap">絞り込み：</label>
                                    <select id="boardCategory" name="category" class="form-select form-select-sm" style="width: auto;">
                                        <option value="all">すべての部署</option>
                                        <option value="dev">開発</option>
                                        <option value="sales">営業</option>
                                        <option value="hr">人事</option>
                                        <option value="finance">財務</option>
                                    </select>
                            </div>
                    </div>
                    

                    <div class="d-flex flex-column gap-3">

                        <div class="card border-1 shadow-sm p-3">
                            <div class="tags mb-2">
                                <span class="badge bg-danger-subtle text-danger">開発</span>
                                <span class="badge bg-light text-dark">全社お知らせ</span>
                                <span class="badge bg-primary-subtle text-primary"># ITシステム部</span>
                            </div>
                            <h3 class="h6 fw-hold mb-2">【重要】来期における社内セキュリティガイドライン改定について</h3>
                            <p class="text-secondary small mb-0">
                                社員の皆様お疲れ様です。ITシステム部の山田です。近年増加している標的型攻撃メール対策に伴い...
                            </p>

                            <hr class="border-gray-100 mb-3" />

                                <!-- フッターエリア（投稿者情報 & メトリクス） -->
                                <div class="flex items-center justify-between text-xs text-gray-400">
                                    <!-- 投稿者 -->
                                    <div class="d-flex align-items-center gap-2">
                                        <div>
                                            <span class="text-muted small">近藤 駆</span>
                                            <span class="text-muted small">ITシステム部 ソフトウェアエンジニア</span>
                                        </div>
                                            <span class="text-muted ms-auto small">2026-10-23</span>
                                        
                                    </div>
                                </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>

    </main>
</body>
</html>
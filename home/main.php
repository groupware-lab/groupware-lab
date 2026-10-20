<?php
require_once __DIR__ . '/../common/auth_check.php';      
require_once __DIR__ . '/../common/get_user_info.php';
require_once __DIR__ . '/../common/approver.php';

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
            <div class="col-12">
                <div class="card border border-1 shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 fw-bold mb-0">社内掲示板</h2>
                        <a href="#" class="btn btn-sm btn-link text-decoration-none p-0">一覧を見る &raquo;</a>
                    </div>
                    <!-- ここはデモ用の表示---のちほどphpでＤＢから取得してデータ表示する -->
                    <div class="list-group list-group-flush border-top border-bottom">
                        <a href="#" class="list-group-item list-group-item-action px-2 py-2 d-flex align-items-center gap-3">
                            <span class="text-muted small">2026/10/15</span>
                            <span class="badge bg-danger">重要</span>
                            <span class="text-truncate flex-grow-1">【重要】社内システム一時停止のお知らせ（10月20日 18:00〜）</span>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action px-2 py-2 d-flex align-items-center gap-3">
                            <span class="text-muted small">2026/10/12</span>
                            <span class="badge bg-primary">お知らせ</span>
                            <span class="text-truncate flex-grow-1">秋の社内懇親会 開催のご案内</span>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action px-2 py-2 d-flex align-items-center gap-3">
                            <span class="text-muted small">2026/10/08</span>
                            <span class="badge bg-secondary">総務</span>
                            <span class="text-truncate flex-grow-1">出退勤打刻ルールの変更（スマホアプリ対応）について</span>
                        </a>

                        <a href="#" class="list-group-item list-group-item-action px-2 py-2 d-flex align-items-center gap-3">
                            <span class="text-muted small">2026/10/01</span>
                            <span class="badge bg-info text-dark">研修</span>
                            <span class="text-truncate flex-grow-1">【募集】ITセキュリティ研修 開催のご案内</span>
                        </a>
                    <!-- ここはデモ用の表示---のちほどphpでＤＢから取得してデータ表示する -->
                    </div>
                </div>
            </div>

            <!-- 出退勤管理 -->
            <div class="col-md-6">
                <div class="card border border-1 shadow-sm p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <h2 class="h6 fw-bold mb-4">出退勤管理</h2>
                        
                        <div class="bg-light border rounded py-4 px-3 text-center mb-4">
                            <div id="clock-date" class="text-muted small fw-bold mb-2">----年--月--日(-)</div>
                            <div id="clock-time" class="display-5 fw-bold text-dark clock-time">00:00:00</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <button type="button" class="btn btn-primary w-100 fw-bold py-2">出勤</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-secondary w-100 fw-bold py-2">退勤</button>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                /* 時計表示用のフォントとスタイル調整 */
                .clock-time {
                    font-family: 'Courier New', Courier, monospace;
                    letter-spacing: 2px;
                }
            </style>


            <!-- ワークフロー -->
            <div class="col-md-6">
                <div class="card border border-1 shadow-sm p-4 h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h2 class="h6 fw-bold mb-0">ワークフロー</h2>
                        <a href="../workflow/workflow_main.php" class="btn btn-sm btn-link text-decoration-none p-0">ワークフローへ &raquo;</a>
                    </div>
                    <!-- 件数は仮表示---のちほどphpでＤＢから取得する -->
                    <div class="list-group list-group-flush border-top border-bottom mb-4">
                        <a href="../workflow/workflow_main.php" class="list-group-item list-group-item-action px-2 py-3 d-flex justify-content-between align-items-center">
                            <span>申請中の稟議</span>
                            <span class="fs-4">〇〇件</span>
                        </a>

                        <?php if ($isApprover): ?>
                        <a href="../ringi/approval_list.php" class="list-group-item list-group-item-action px-2 py-3 d-flex justify-content-between align-items-center">
                            <span>承認待ちの稟議</span>
                            <span class="fs-4">〇〇件</span>
                        </a>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3 mt-auto">
                        <div class="<?= $isApprover ? 'col-6' : 'col-12'?>">
                            <a href="../ringi/ringi.php" class="btn btn-primary w-100 fw-bold py-2">稟議を申請</a>
                        </div>
                        <?php if ($isApprover): ?>
                        <div class="col-6">
                            <a href="../ringi/approval_list.php" class="btn btn-outline-primary w-100 fw-bold py-2">承認待ちを見る</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- 時計更新用JS -->
    <script>
        function updateAttendanceClock() {
            const now = new Date();

            const year = now.getFullYear();
            const month = now.getMonth() + 1;
            const date = now.getDate();
            const dayList = ['日', '月', '火', '水', '木', '金', '土'];
            const day = dayList[now.getDay()];

            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');

            document.getElementById('clock-date').textContent = `${year}年${month}月${date}日(${day})`;
            document.getElementById('clock-time').textContent = `${hours}:${minutes}:${seconds}`;
        }

        setInterval(updateAttendanceClock, 1000);
        updateAttendanceClock();
    </script>
</body>
</html>
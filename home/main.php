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
    <title>My Web Page</title>
    <!-- assets内のBootstrap CSSを読み込み -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
</head>
<body class="p-3">

    <header class="mb-4">
        <!-- タイトル -->
        <div class="fw-bold fs-5 mb-3">株式会社コンドウソリューションズ</div>
        
        <!-- ナビゲーションメニュー（枠線・内側の余白はBootstrapクラス） -->
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
        <!-- 最上部のカード -->
        <section class="card border-dark rounded-0 mb-3">
            <div class="card-header bg-white border-dark rounded-0 fw-bold">出退勤管理</div>
            <div class="card-body py-5"></div>
        </section>

        <!-- 2列レイアウト -->
        <div class="container-fluid p-0">
            <div class="row g-3">
                <!-- 左列 -->
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

                <!-- 右列 -->
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
<!-- header,navは共通部品のためこちらに分離した -->

<header class="app-header">
    <div class="app-header-inner">
        <span class="app-logo-badge">KS</span>
        <span class="app-header-title">株式会社コンドウソリューション</span>
    </div>

    <nav class="app-nav-menu d-flex align-items-center gap-4">
        <span class="small text-white">
            こんにちは！<?= htmlspecialchars($employee_name, ENT_QUOTES, 'UTF-8') ?>  さん  
        </span>

        <span class="app-user-badge">
            <img src="../assets/img/user_icon.png" alt="社員アイコン" width="30" height="30">
        </span>

        <a href="../home/logout_confirm.php" class="text-white text-decoration-none d-flex align-items-center logout-link" title="ログアウト">
            <img src="../assets/img/logout_icon.png" alt="ログアウト" width="30" height="35" class="me-1 logout-icon">
            <span class="small">ログアウト</span>
        </a>
    </nav>
</header>

<div class="bg-white border-bottom py-2 px-4" mb-2>
    <div class="container-fluid p-0">
        <ul class="d-flex flex-wrap gap-2 list-unstyled mb-0 ps-0">
            <li><a href="../home/main.php" class="btn btn-outline-secondary btn-sm">デスクトップ</a></li>
            <li><a href="#" class="btn btn-outline-secondary btn-sm">出退勤管理</a></li>
            <li><a href="#" class="btn btn-outline-secondary btn-sm">社内掲示板</a></li>
            <li><a href="../workflow/workflow_main.php" class="btn btn-outline-secondary btn-sm">ワークフロー</a></li>
        </ul>
    </div>
</div>
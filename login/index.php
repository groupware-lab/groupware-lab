<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン | 株式会社コンドウソリューション</title>

    <!-- bootstrapのcssを読み込む用 -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- headerの共通部品のcssを読み込む用 -->
    <link href="../assets/css/common.css" rel="stylesheet">

    <!-- 自分で作ったcssを読み込む用 -->
    <link rel="stylesheet" href="style.css">
  </head>

  <body>
    <header class="app-header">
      <div class="app-header-inner">
        <span class="app-logo-badge">KS</span>
        <span class="app-header-title">株式会社コンドウソリューション</span>
      </div>
    </header>

    <div class="login-page">
      <div class="login-card">
        <div class="login-brand">
          <p class="login-brand-en">KONDO SOLUTIONS</p>
          <h1 class="login-title">ログイン</h1>
        </div>

        <form action="login.php" method="post" novalidate>
          <div class="mb-3">
            <label for="employee_id" class="form-label">社員番号</label>
            <input type="text" class="form-control login-input" name="employee_id" id="employee_id" placeholder="社員番号を入力" autocomplete="username">
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" class="form-control login-input" name="password" id="password" placeholder="パスワードを入力" autocomplete="current-password">
          </div>

          <!-- 入力エラーを表示 -->
          <?php
          if (isset($_GET['error'])) {
            if ($_GET['error'] === 'empty') {
              echo '<p class="login-error">社員番号またはパスワードが未入力です。</p>';
            } elseif ($_GET['error'] === 'invalid') {
              echo '<p class="login-error">社員番号またはパスワードに誤りがあります。</p>';
            }
          }
          ?>

          <button type="submit" class="btn login-btn mt-2">ログイン</button>

        </form>
      </div>
    </div>
  </body>
</html>

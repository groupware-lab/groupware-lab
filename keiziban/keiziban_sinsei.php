<?php
require_once __DIR__ . '/../common/auth_check.php';        //セッションチェック
require_once __DIR__ . '/../common/get_user_info.php';     //loginユーザー表示
require_once __DIR__ . '/../common/db.php';                //各種共通部品
require_once __DIR__ . '/../common/approver.php';          //承認ルートの共通部品

//csrfトークンを生成
if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = $_SESSION['ringi_errors'] ?? [];
unset($_SESSION['ringi_errors']);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>登録・編集</title>
  
  <!-- bootstrapのcssを読み込む用 -->
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

  <!-- headerとログアウトボタンの共通部品のcssを読み込む用 -->
  <link href="../common/common.css" rel="stylesheet">

  <!-- 掲示板用のCSSの読み込み -->
  <link href="keiziban_sinsei.css" rel="stylesheet">
</head>

<body class="app-page d-flex flex-column min-vh-100">
  <?php require_once __DIR__ . '/../common/header.php'; ?>
  
  <div class="dialog-wrapper">
    <div class="dialog-header">
      <h1>登録・編集</h1>
    </div>

    <form class="dialog-body">
      <!-- 掲載期間 -->
      <div class="form-row">
        <label class="form-label">掲載期間 <span class="required">*</span></label>
        <div class="form-field period-wrapper">
          <input type="text" class="input-ctrl input-date" value="12/17/2024">
        </div>
      </div>

      <!-- タイトル -->
      <div class="form-row">
        <label class="form-label" for="title">タイトル <span class="required">*</span></label>
        <div class="form-field title-wrapper">
          <input type="text" id="title" class="input-ctrl input-title" value="年末年始休暇のお知らせ">
          <label class="custom-checkbox">
            <input type="checkbox" checked>
            <span>重要</span>
          </label>
        </div>
      </div>

      <!-- 部署選択 -->
      <div class="form-row">
        <label class="form-label" for="dept">部署選択</label>
        <div class="form-field">
          <select id="dept" class="input-ctrl select-dept">
            <option value="">部署を選択してください</option>
            <option value="dev">開発部</option>
            <option value="sales">営業部</option>
            <option value="hr">人事部</option>
            <option value="finance">財務部</option>
          </select>
        </div>
      </div>

      <!-- 掲示者 -->
      <div class="form-row">
        <span class="form-label">掲示者</span>
        <div class="form-field poster-wrapper">
          <span>部署名 </span>
          <span>職位 </span>
          <span>氏名 </span>
        </div>
      </div>

      <!-- 内容 -->
      <fieldset class="content-box">
        <legend>内容 <span class="required">*</span></legend>
        <textarea class="content-textarea">年末年始休暇のお知らせです。</textarea>
      </fieldset>

        <div class="footer-buttons">
          <button type="submit" class="btn-action btn-submit">登録</button>
          <button type="button" class="btn-action btn-cancel">閉じる</button>
        </div>
      </div>
    </form>
  </div>
</body>
</html>
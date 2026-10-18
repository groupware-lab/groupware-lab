<?php
require_once __DIR__ . '/../common/auth_check.php';  
require_once __DIR__ . '/../common/db.php';   
require_once __DIR__ . '/../common/get_user_info.php'; 
require_once __DIR__ . '/../common/approver.php';
require_once __DIR__ . '/../common/ringi_status.php';

// 承認者かどうか(稟議を承認できる職位か)
$canApproveRingi = canApprove($loginUser['position'] ?? null, 'ringi');
$isApprover = $canApproveRingi;

//ログインしてる人のセッションを取得（申請者のみの稟議内容を取得）
$applicant_id = $_SESSION['user_id'];

// 稟議の承認待ち件数
$ringiPendingCount = 0;
if ($canApproveRingi) {
    $stmt = $connection->prepare(
        "SELECT COUNT(*)
         FROM ringi_approvers ra
         JOIN ringi r ON r.id = ra.ringi_id
         WHERE ra.approver_id = :me
           AND ra.step_order = r.current_step
           AND ra.status = 'waiting'
           AND r.status  = 'pending'"
    );
    $stmt->bindValue(':me', $applicant_id, PDO::PARAM_INT);
    $stmt->execute();
    $ringiPendingCount = (int)$stmt->fetchColumn();
}

// 申請完了などのメッセージ
$flash = $_SESSION['flash_message'] ?? '';
unset($_SESSION['flash_message']);

//稟議id,プロジェクト名,費用,申請状況,申請日を取得
$sql = "SELECT ringi.id, ringi.project_name, ringi.estimated_cost, ringi.status, ringi.created_at,
        users.name AS current_approver
        FROM ringi
        LEFT JOIN ringi_approvers
          ON ringi_approvers.ringi_id = ringi.id
          AND ringi_approvers.step_order = ringi.current_step
        LEFT JOIN users
          ON users.id = ringi_approvers.approver_id
        WHERE ringi.applicant_id = :applicant_id
        ORDER BY ringi.created_at DESC
        LIMIT 5";

$stmt = $connection->prepare($sql);

$stmt->bindValue(':applicant_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$histories = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <?php if ($flash !== ''): ?>
      <div class="alert alert-success"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="row g-3">
      <div class="<?= $isApprover ? 'col-12' : 'col-md-6' ?>">
        <div class="card border border-1 shadow-sm p-3 h-100">
          <h2 class="h6 fw-bold mb-3">申請履歴</h2>

          <?php if (empty($histories)): ?>
            <p class="text-muted small mb-0">申請履歴はまだありません。</p>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-2">
              <thead class="table-light">
                <tr>
                  <th>申請日</th>
                  <th>種別</th>
                  <th>プロジェクト名</th>
                  <th class="text-end pe-4">費用見積</th>
                  <th>ステータス</th>
                  <th>現在の承認者</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($histories as $h): ?>
                  <?php $s = ringiStatusLabel($h['status']); ?>
                  <tr>
                    <td><?= date('Y/m/d', strtotime($h['created_at'])) ?></td>
                    <td>稟議書</td>
                    <td>
                      <a href="#"><?= htmlspecialchars($h['project_name'], ENT_QUOTES, 'UTF-8') ?></a>
                    </td>
                    <td class="text-end pe-4"><?= number_format((int)$h['estimated_cost']) ?>円</td>
                    <td>
                      <span class="badge <?= $s['class'] ?>">
                        <?= htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8') ?>
                      </span>
                    </td>
                    <td>
                      <?= $h['status'] === 'pending'
                        ? htmlspecialchars($h['current_approver'] ?? '-', ENT_QUOTES, 'UTF-8') : '-' ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <a href="#" class="small">すべての申請状況の詳細を見る⇒</a>
          <?php endif; ?>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card border border-1 shadow-sm p-3 text-center h-100">
        <h2 class="h6 fw-bold mb-3">新規ワークフロー申請</h2>

        <h3 class="h6 fw-bold mb-2">稟議書</h3>
        <a href="../ringi/ringi.php" class="btn bg-light w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
          <img src="../assets/img/sinsei.png" alt="ファイルの画像" style="max-height: 20px; width: auto;">
          <span>申請</span>
        </a>

        <h3 class="h6 fw-bold mb-2">有給申請</h3>
        <span class="btn bg-light w-100 mb-3 text-muted disabled">準備中</span>

        <h3 class="h6 fw-bold mb-2">備品購入申請</h3>
        <span class="btn bg-light w-100 text-muted disabled">準備中</span>
      </div>
    </div>

    <!-- 承認待ち(承認者のみ) -->
    <?php if ($isApprover): ?>
    <div class="col-md-6">
      <div class="card border border-1 shadow-sm p-3 text-center h-100">
        <h2 class="h6 fw-bold mb-3">
          承認待ち
          <?php if ($ringiPendingCount > 0): ?>
            <span class="badge bg-danger ms-1"><?= $ringiPendingCount ?></span>
          <?php endif; ?>
        </h2>

        <h3 class="h6 fw-bold mb-2">稟議書</h3>
        <?php if ($ringiPendingCount > 0): ?>
          <a href="../ringi/approval_list.php" class="btn bg-light w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
            <img src="../assets/img/syounin.png" alt="ファイルの画像" style="max-height: 20px; width: auto;">
            <span>承認待ち <?= $ringiPendingCount ?>件</span>
          </a>
        <?php else: ?>
          <span class="btn bg-light w-100 mb-3 text-muted disabled">承認待ちはありません</span>
        <?php endif; ?>

        <h3 class="h6 fw-bold mb-2">有給申請</h3>
        <span class="btn bg-light w-100 mb-3 text-muted disabled">準備中</span>

        <h3 class="h6 fw-bold mb-2">備品購入申請</h3>
        <span class="btn bg-light w-100 text-muted disabled">準備中</span>
      </div>
    </div>
    <?php endif; ?>
  </div>
</main>
  </body>
</html>
<?php
  require_once __DIR__ . '/../common/auth_check.php';
  require_once __DIR__ . '/../common/db.php';
  require_once __DIR__ . '/../common/get_user_info.php';
  require_once __DIR__ . '/../common/approver.php';

  //承認者(課長以上)でなければワークフロー画面へ戻す
  if (!canApprove($loginUser['position'] ?? null, 'yuukyuu')) {
    header('Location: ../workflow/workflow_main.php');
    exit;
  }

  $flash = $_SESSION['flash_message'] ?? '';
  unset($_SESSION['flash_message']);

  $approverId = (int)$_SESSION['user_id'];
  
  //有給内容、承認テーブル、ユーザテーブルを結合して申請中の有給を昇順に取得
  $sql ="SELECT yuukyuu.id,
                yuukyuu.leave_type,
                yuukyuu.start_date,
                yuukyuu.end_date,
                yuukyuu.days,
                yuukyuu.current_step,
                yuukyuu.created_at,
                users.name AS applicant_name,
                users.department AS applicant_department,
                (SELECT COUNT(*)
                  FROM yuukyuu_approvers AS steps
                  WHERE steps.yuukyuu_id = yuukyuu.id) AS total_steps
          FROM yuukyuu_approvers
            INNER JOIN yuukyuu ON yuukyuu.id = yuukyuu_approvers.yuukyuu_id
            INNER JOIN users ON users.id = yuukyuu.applicant_id
          WHERE yuukyuu_approvers.approver_id = :approver_id
            AND yuukyuu_approvers.step_order = yuukyuu.current_step
            AND yuukyuu_approvers.status = 'waiting'
            AND yuukyuu.status = 'pending'
          ORDER BY yuukyuu.created_at ASC";

  //sqlをDBにわたす
  $stmt = $connection->prepare($sql);
  $stmt->bindValue(':approver_id', $approverId, PDO::PARAM_INT);
  $stmt->execute();
  $pendingList = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>承認待ち一覧</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../common/common.css" rel="stylesheet">
  </head>

  <body class="app-page d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../common/header.php';?>

    <main class="container-fluid px-4 flex-grow-1">
      <?php if($flash !== ''): ?>
        <div class="alert alert-info mt-3"><?=  htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <div class="card border border-1 shadow-sm p-3 mt-3">
        <h2 class="h6 fw-bold mb-3">承認待ち一覧</h2>
        <?php if(empty($pendingList)): ?>
          <p class="text-muted small mb-0">承認待ちの有給申請はありません。</p>
        <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>申請No</th>
                <th>申請日</th>
                <th>申請者</th>
                <th>休暇種別</th>
                <th>取得期間</th>
                <th>取得日数</th>
                <th>承認段階</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach($pendingList as $row): ?>
              <tr class="position-relative">
                <!-- 申請No -->
                <td>
                  <a href="yuukyuu_detail.php?id=<?= (int)$row['id'] ?>" class="stretched-link text-dark text-decoration-none">
                    <?= sprintf('Y-%04d', $row['id']) ?>
                  </a>
                </td>
                <!-- 申請日 -->
                <td><?= date('Y/m/d', strtotime($row['created_at'])) ?></td>
                <!-- 申請者名 -->
                <td><?= htmlspecialchars($row['applicant_name'], ENT_QUOTES, 'UTF-8') ?>
                <!-- 部署名 -->
                  <div class="small text-muted">
                    <?= htmlspecialchars($row['applicant_department'], ENT_QUOTES, 'UTF-8') ?>
                  </div>
                </td>
                <!-- 休暇種別 -->
                <td><?= htmlspecialchars($row['leave_type'], ENT_QUOTES,'UTF-8') ?></td>
                <!-- 取得期間 -->
                <td><?=  date('Y/m/d', strtotime($row['start_date'])), '～', date('Y/m/d', strtotime($row['end_date'])) ?></td>
                <!-- 取得日数 -->
                <td><?= htmlspecialchars($row['days'], ENT_QUOTES, 'UTF-8') ?></td>
                <!-- 承認段階 -->
                <td><?= (int)$row['current_step'] ?>/<?= (int)$row['total_steps'] ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>      
          </table>
        </div>
        <?php endif; ?>
        <a href="../workflow/workflow_main.php" class="small d-inline-block mt-3">← ワークフローに戻る</a>
      </div>
    </main>
  </body>
</html>
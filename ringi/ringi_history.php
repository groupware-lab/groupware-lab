<?php
require_once __DIR__ . '/../common/auth_check.php';  
require_once __DIR__ . '/../common/db.php';   
require_once __DIR__ . '/../common/get_user_info.php'; 
require_once __DIR__ . '/../common/ringi_status.php';

//ログインしてる人のセッションを取得（申請者のみの稟議内容を取得）
$applicant_id = $_SESSION['user_id'];


//稟議id,プロジェクト名,費用,申請状況,申請日を取得
$sql = "SELECT ringi.id, 
        ringi.project_name, 
        ringi.estimated_cost, 
        ringi.status, 
        ringi.created_at,
        users.name AS current_approver
        FROM ringi
        LEFT JOIN ringi_approvers
          ON ringi_approvers.ringi_id = ringi.id
          AND ringi_approvers.step_order = ringi.current_step
        LEFT JOIN users
          ON users.id = ringi_approvers.approver_id
        WHERE ringi.applicant_id = :applicant_id
        ORDER BY ringi.created_at DESC";

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
    <title>申請履歴</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../common/common.css" rel="stylesheet">
  </head>

  <body class="app-page d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../common/header.php';?>

    <main class="container-fluid px-4 flex-grow-1">
      <div class="card border border-1 shadow-sm p-3 mt-3">
        <h2 class="h6 fw-bold mb-3">申請履歴</h2>

        <?php if(empty($histories)): ?>
          <p class="text-muted small mb-0">申請履歴はまだありません。</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>稟議No</th>
                  <th>申請日</th>
                  <th>件名</th>
                  <th class="text-end pe-4">費用見積</th>
                  <th>ステータス</th>
                  <th>現在の承認者</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach($histories as $h): ?>
                <?php $s = ringiStatusLabel($h['status']); ?>
                <tr>
                  <td><?= sprintf('R-%04d', $h['id']) ?></td>
                  <td><?= date('Y/m/d', strtotime($h['created_at'])) ?></td>
                  
                  <td>
                    <a href="approval_detail.php?id=<?= (int)$h['id'] ?>">
                      <?= htmlspecialchars($h['project_name'], ENT_QUOTES, 'UTF-8') ?>
                    </a>
                  </td>
                  <td class="text-end pe-4"><?= number_format((int)$h['estimated_cost']) ?>円</td>
                  
                  <td>
                    <span class="badge <?= $s['class']?>">
                      <?=  htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                  <td>
                    <?= $h['status'] === 'pending' ? htmlspecialchars($h['current_approver'] ?? '-', ENT_QUOTES, 'UTF-8'): '-' ?>
                  </td>
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

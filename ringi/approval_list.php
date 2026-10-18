<?php
  require_once __DIR__ . '/../common/auth_check.php';
  require_once __DIR__ . '/../common/db.php';
  require_once __DIR__ . '/../common/get_user_info.php';

  $approverId = (int)$_SESSION['user_id'];
  //稟議内容、承認テーブル、ユーザテーブルを結合して申請中の稟議を昇順に取得
  $sql ="SELECT ringi.id,
                ringi.project_name,
                ringi.estimated_cost,
                ringi.current_step,
                ringi.created_at,
                users.name AS applicant_name,
                users.department AS applicant_department,
                (SELECT COUNT(*)
                  FROM ringi_approvers AS steps
                  WHERE steps.ringi_id = ringi.id) AS total_steps
          FROM ringi_approvers
            INNER JOIN ringi ON ringi.id = ringi_approvers.ringi_id
            INNER JOIN users ON users.id = ringi.applicant_id
          WHERE ringi_approvers.approver_id = :approver_id
            AND ringi_approvers.step_order = ringi.current_step
            AND ringi_approvers.status = 'waiting'
            AND ringi.status = 'pending'
          ORDER BY ringi.created_at ASC";

  //sqlをDBにわたす
  $stmt = $connection->prepare($sql);
  $stmt->bindValue('approver_id', $approverId, PDO::PARAM_INT);
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

  <body class="bg-light d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../common/header.php';?>

    <main class="container-fluid px-4 flex-grow-1">
      <div class="card border border-1 shadow-sm p-3 mt-3">
        <h2 class="h6 fw-bold mb-3">承認待ち一覧</h2>
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th>稟議No</th>
                <th>申請日</th>
                <th>申請者</th>
                <th>件名</th>
                <th class="text-end">費用見積</th>
                <th>承認段階</th>
              </tr>
            </thead>

            <tbody>
              <?php foreach($pendingList as $row): ?>
              <tr>
                <td><?=  sprintf('R-%04d', $row['id']) ?></td>
                <td><?=  date('Y/m/d', strtotime($row['created_at'])) ?></td>
                <td><?=  htmlspecialchars($row['applicant_name'], ENT_QUOTES, 'UTF-8') ?>
                  <div class="small text-muted"><?= htmlspecialchars($row['applicant_department'], ENT_QUOTES, 'UTF-8') ?></div>
                </td>
                <td>
                  <a href="approval_detail.php?id=<?= (int)$row['id'] ?>">
                    <?= htmlspecialchars($row['project_name'], ENT_QUOTES, 'UTF-8') ?>   
                  </a>
                </td>
                <td class="text-end"><?= number_format((int)$row['estimated_cost'])?>円</td>
                <td><?= (int)$row['current_step'] ?>/<?= (int)$row['total_steps'] ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>      
          </table>
        </div>

        <a href="../workflow/workflow_main.php" class="small d-inline-block mt-3">← ワークフローに戻る</a>

      </div>
    </main>
  </body>
</html>
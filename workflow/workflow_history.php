<?php
require_once __DIR__ . '/../common/auth_check.php';  
require_once __DIR__ . '/../common/db.php';   
require_once __DIR__ . '/../common/get_user_info.php'; 
require_once __DIR__ . '/../common/ringi_status.php';

//ログインしてる人のセッションを取得（申請者のみの稟議内容を取得）
$applicant_id = $_SESSION['user_id'];

//申請履歴表示の実装
$type = $_GET['type'] ?? 'ringi';
if(!in_array($type, ['ringi', 'yuukyuu'], true)) {
  $type = 'ringi';
}  

//申請履歴を表示　　稟議書か有給申請か備品申請か分岐
if($type === 'ringi') {
  //id,　プロジェクト名,　費用,　申請状況,　申請日を取得
  $sql = "SELECT ringi.id, ringi.project_name, ringi.estimated_cost, ringi.status, ringi.created_at,
          users.name AS current_approver
          FROM ringi
          LEFT JOIN ringi_approvers
            ON ringi_approvers.ringi_id = ringi.id
            AND ringi_approvers.step_order = ringi.current_step
          LEFT JOIN users
            ON users.id = ringi_approvers.approver_id
          WHERE ringi.applicant_id = :applicant_id
          ORDER BY ringi.created_at DESC";
} elseif($type === 'yuukyuu') {

//id, 有給開始日,　終了日,　取得日数,　申請状況,　申請日を取得
  $sql = "SELECT yuukyuu.id, leave_type, start_date, end_date, days, yuukyuu.status, yuukyuu.created_at,
          users.name AS current_approver
          FROM yuukyuu
          LEFT JOIN yuukyuu_approvers
            ON yuukyuu_approvers.yuukyuu_id = yuukyuu.id
            AND yuukyuu_approvers.step_order = yuukyuu.current_step
          LEFT JOIN users
            ON users.id = yuukyuu_approvers.approver_id
          WHERE yuukyuu.applicant_id = :applicant_id
          ORDER BY yuukyuu.created_at DESC";
} 

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
        <div class="d-flex justify-content-between align-items-center">

          <h2 class="h6 fw-bold mb-3">全件：申請履歴</h2>
          <form method="get">
              <select name="type" class="form-select form-select-sm" onchange="this.form.submit();">
                <option value="ringi" <?= $type === 'ringi' ? 'selected' : '' ?>>稟議書</option>
                <option value="yuukyuu" <?= $type === 'yuukyuu' ? 'selected' : '' ?>>有給申請</option>
              </select>
          </form>
        </div>

          <?php if(empty($histories)): ?>
            <p class="text-muted small mb-0">申請履歴はまだありません。</p>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>申請No</th>
                  <th>申請日</th>
                  <?php if($type === 'ringi'):?>
                    <th>件名</th>
                    <th class="text-end pe-4">費用見積</th>
                  <?php elseif($type === 'yuukyuu'):?>
                    <th>休暇種別</th>
                    <th>取得期間</th>
                    <th>取得日数</th>
                  <?php endif; ?>
                    <th>ステータス</th>
                    <th>現在の承認者</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach($histories as $h): ?>
                <?php $s = ringiStatusLabel($h['status']); ?>
                <tr>
                  <!-- 申請NO -->
                   <?php $prefix = ($type === 'ringi') ? 'R' : 'Y'; ?> 
                  <td><?= sprintf('%s-%04d', $prefix, $h['id']) ?></td>
                  <!-- 申請日 -->
                  <td><?= date('Y/m/d', strtotime($h['created_at'])) ?></td>
                  <?php if($type === 'ringi'): ?>
                    <!-- プロジェクト名 -->
                    <td>
                      <a href="../ringi/approval_detail.php?id=<?= (int)$h['id'] ?>">
                        <?= htmlspecialchars($h['project_name'], ENT_QUOTES, 'UTF-8') ?>
                      </a>
                    <!-- 費用見積 -->
                    </td>
                    <td class="text-end pe-4"><?= number_format((int)$h['estimated_cost']) ?>円</td>
                    <?php elseif($type === 'yuukyuu'): ?>
                      <!-- 休暇種別 -->
                      <td><?=  htmlspecialchars($h['leave_type'], ENT_QUOTES, 'UTF-8')?></td>
                      <!-- 取得期間 -->
                      <td><?=  date('Y/m/d', strtotime($h['start_date'])), '～', date('Y/m/d', strtotime($h['end_date'])) ?></td>
                      <!-- 取得日数 -->
                      <td><?=  htmlspecialchars($h['days'], ENT_QUOTES, 'UTF-8')?></td>
                      <?php endif; ?>
                      <!-- 承認ステータス -->
                      <td>
                        <span class="badge <?= $s['class']?>">
                          <?= htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                      </td>
                      <!-- 現在の承認者 -->
                      <td>
                        <?= $h['status'] === 'pending' ? htmlspecialchars($h['current_approver'] ?? '-', ENT_QUOTES, 'UTF-8'): '-' ?>
                      </td>
                    </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

        <a href="workflow_main.php" class="small d-inline-block mt-3">← ワークフローに戻る</a>
      </div>
    </main>
  </body>
</html>

<?php
require_once __DIR__ . '/../common/auth_check.php';  
require_once __DIR__ . '/../common/db.php';   
require_once __DIR__ . '/../common/get_user_info.php'; 

//ログインしてる人のセッションを取得（申請者のみの稟議内容を取得）
$applicant_id = $_SESSION['user_id'];

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
$histories = $stmt->fetchALL(PDO::FETCH_ASSOC);

//申請履歴のステータスメッセージを表示
$status_labels = [
  'pending'  => ['label' => '承認待ち', 'class' => 'bg-warning text-dark'],
  'approved' => ['label' => '承認済み', 'class' => 'bg-success'],
  'rejected' => ['label' => '却下', 'class' => 'bg-danger'],
  ];
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
        <div class="row g-3">
          <div class="col-12">
            <div class="card border border-1 shadow-sm p-3">
              <h2 class="h6 fw-bold mb-3">申請履歴</h2>

              <?php if(empty($histories)): ?>
                <p class='text-muted small mb-0'>申請履歴はまだありません。</p>
              <?php else: ?>
                <div class='table-responsive'>
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
                      <?php
                        $s  = $status_labels[$h['status']]
                          ??['label' => $h['status'], 'class' => 'bg-light text-dark' ];
                      ?>
                      <tr>
                        <td><?= date('Y/m/d', strtotime($h['created_at']))?></td>
                        <td>稟議書</td>
                        <td>
                          <a href="#">
                          <?= htmlspecialchars($h['project_name'], ENT_QUOTES, 'UTF-8') ?>
                          </a>
                        </td>

                        <td class="text-end pe-4"><?= number_format((int)$h['estimated_cost']) ?>円</td>
                        <td>
                          <span class="badge  <?= $s['class'] ?>">
                            <?=  htmlspecialchars($s['label'], ENT_QUOTES, 'UTF-8') ?>
                          </span>
                        </td>
                        <td>
                          <?=  $h['status'] === 'pending'
                          ? htmlspecialchars($h['current_approver'] ?? '-', ENT_QUOTES, 'UTF-8'): '-' ?>
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
          <div class="card border border-1 shadow-sm p-3 text-center">
            <h2 class="h6 fw-bold mb-3">新規ワークフロー申請</h2>
            <h3 class="h6 fw-bold mb-2">稟議書</h3>
            <a href="../ringi/ringi.php" class="btn bg-light w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
              <img src="../assets/img/sinsei.png" alt="申請" style="max-height: 20px; width: auto;">
              <span>申請</span>
            </a>

            <h3 class="h6 fw-bold mb-2">有給申請</h3>
            <a href="#" class="btn bg-light w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
              <img src="../assets/img/sinsei.png" alt="申請" style="max-height: 20px; width: auto;">
              <span>申請</span>
            </a>

            <h3 class="h6 fw-bold mb-2">備品購入申請</h3>
            <a href="#" class="btn bg-light w-100 d-flex align-items-center justify-content-center gap-2">
              <img src="../assets/img/sinsei.png" alt="申請" style="max-height: 20px; width: auto;">
              <span>申請</span>
            </a>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card border border-1 shadow-sm p-3 text-center">
            <h2 class="h6 fw-bold mb-3">新規ワークフロー承認</h2>
          
            <h3 class="h6 fw-bold mb-2">稟議書</h3>
            <a href="#" class="btn bg-light w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
              <img src="../assets/img/syounin.png" alt="承認" style="max-height: 20px; width: auto;">
              <span>承認</span>
            </a>

            <h3 class="h6 fw-bold mb-2">有給申請</h3>
              <a href="#" class="btn bg-light w-100 d-flex align-items-center justify-content-center gap-2 mb-3">
                <img src="../assets/img/syounin.png" alt="承認" style="max-height: 20px; width: auto;"> 
                <span>承認</span>
              </a>

              <h3 class="h6 fw-bold mb-2">備品購入申請</h3>
              <a href="#" class="btn bg-light w-100 d-flex align-items-center justify-content-center gap-2">
                <img src="../assets/img/syounin.png" alt="承認" style="max-height: 20px; width: auto;">
                <span>承認</span>
              </a>
            </div>
          </div>
        </div>
      </main>
  </body>
</html>
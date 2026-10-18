<?php
  require_once __DIR__ . '/../common/auth_check.php';
  require_once __DIR__ . '/../common/db.php';
  require_once __DIR__ . '/../common/get_user_info.php';
  require_once __DIR__ . '/../common/ringi_status.php';

  //ログインしている人のID
  $loginUserId = (int)$_SESSION['user_id'];

  //URLから id を受け取る(なければ空文字)
  $idRaw = $_GET['id'] ?? '';

  //数字だけでできているか確認→違えば一覧へ戻す
  if(!is_string($idRaw) || !ctype_digit($idRaw)) {
    header('Location: approval_list.php');
    exit;
  }

  //数値に変換して使う
  $ringiId = (int)$idRaw;

  //稟議の申請内容を取得する用
  $sql = "SELECT ringi.id, 
                 ringi.project_name, 
                 ringi.content,
                 ringi.estimated_cost, 
                 ringi.status, 
                 ringi.current_step, 
                 ringi.created_at,
                 users.name AS applicant_name,
                 users.department AS applicant_department,
                 users.position AS applicant_position
          FROM ringi
            INNER JOIN users ON users.id = ringi.applicant_id
          WHERE ringi.id = :ringi_id";

  $stmt = $connection->prepare($sql);
  $stmt->bindValue(':ringi_id', $ringiId, PDO::PARAM_INT);
  $stmt->execute();
  $ringi = $stmt->fetch(PDO::FETCH_ASSOC);

  //存在しない稟議なら一覧へ戻す
  if($ringi === false) {
    header('Location: approval_list.php');
    exit;
  }

  //承認ルートを取得する用
  $sql = "SELECT ringi_approvers.step_order,
                 ringi_approvers.approver_id,
                 ringi_approvers.status,
                 ringi_approvers.comment,
                 ringi_approvers.approved_at,
                 users.name AS approver_name,
                 users.department AS approver_department,
                 users.position AS approver_position
          FROM ringi_approvers
            INNER JOIN users ON users.id =  ringi_approvers.approver_id
          WHERE ringi_approvers.ringi_id = :ringi_id
          ORDER BY ringi_approvers.step_order ASC";

  $stmt = $connection->prepare($sql);
  $stmt->bindValue(':ringi_id',$ringiId, PDO::PARAM_INT);
  $stmt->execute();
  $approvers =$stmt->fetchAll(PDO::FETCH_ASSOC);

  //-----アクセス制御---権限系の確認-------　申請者本人か確認 
  $isApplicant = ((int) $ringi['applicant_id'] === $loginUserId);

  //承認ルートに入っているか
  $isInRoute = false;
  $canOperate =false;
  foreach($approvers as $a) {
    if((int)$a['approver_id'] === $loginUserId) {
      $isInRoute = true;
     if ((int)$a['step_order'] === (int)$ringi['current_step']
          && $a['status'] === 'waiting'
          && $ringi['status'] === 'pending') {
        $canOperate = true;
      }
    }
  }

  //戻り先(申請者はワークフロー画面、承認者は承認待ち一覧)
  $backUrl   = $isApplicant ? '../workflow/workflow_main.php' : 'approval_list.php';
  $backLabel = $isApplicant ? '← ワークフローに戻る' : '← 承認待ち一覧に戻る';


  //CSRFトークンを生成
  if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }

  //稟議全体のステータス表示
  $ringiStatus = ringiStatusLabel($ringi['status']);

  //承認ルートの各行に、表示用のラベル・色を付け足す
  $route = [];
  foreach ($approvers as $a) {
    if ($a['status'] === 'approved') {
      $a['label'] = '承認済み'; $a['badge'] = 'bg-success';   $a['row_class'] = 'list-group-item-success';
    } elseif ($a['status'] === 'returned') {
      $a['label'] = '差し戻し'; $a['badge'] = 'bg-secondary'; $a['row_class'] = 'list-group-item-secondary';
    } elseif ($a['status'] === 'rejected') {
      $a['label'] = '却下';     $a['badge'] = 'bg-danger';    $a['row_class'] = 'list-group-item-danger';
    } elseif ((int)$a['step_order'] === (int)$ringi['current_step'] && $ringi['status'] === 'pending') {
      $a['label'] = '承認待ち'; $a['badge'] = 'bg-warning text-dark'; $a['row_class'] = 'list-group-item-warning';
    } else {
      $a['label'] = '未到達';   $a['badge'] = 'bg-light text-dark border'; $a['row_class'] = '';
    }
    $route[] = $a;
  }
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>稟議書 詳細</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../common/common.css" rel="stylesheet">
  </head>

  <body class="bg-light d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../common/header.php'; ?>

    <main class="container-fluid px-4 flex-grow-1">
      <div class="row g-3 mt-1">
        <!-- 稟議の内容 -->
        <div class="col-lg-8">
          <div class="card border border-1 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h2 class="h6 fw-bold mb-0">稟議書<?= sprintf('R-%04d', $ringi['id']) ?></h2>
              <span class="badge<?=  $ringiStatus['class'] ?>">
                <?= htmlspecialchars($ringiStatus['label'], ENT_QUOTES, 'UTF-8')?>
              </span>
            </div>

            <table class="table table-bordered mb-4">
              <tbody>
                <tr>
                  <th class="table-light" style="width: 25%;">申請日</th>
                  <td><?= date('Y/m/d', strtotime($ringi['created_at'])) ?></td>
                </tr>
                <tr>
                  <th class="table-light">申請者</th>
                  <td><?= htmlspecialchars(trim($ringi['applicant_department'] . ' ' . $ringi['applicant_position'] . ' ' . $ringi['applicant_name']), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                  <th class="table-light">プロジェクト名</th>
                  <td><?= htmlspecialchars($ringi['project_name'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                  <th class="table-light">費用見積</th>
                  <td><?= number_format((int)$ringi['estimated_cost']) ?>円</td>
                </tr>
              </tbody>
            </table>

            <h3 class="h6 fw-bold">稟議内容</h3>
            <div class="border rounded p-3 bg-light">
              <?= nl2br(htmlspecialchars($ringi['content'], ENT_QUOTES, 'UTF-8')) ?>
            </div>
            <a href="<?= $backUrl ?>" class="small d-inline-block mt-4"><?= $backLabel ?></a>
          </div>
        </div>

         <!-- 承認ルートと操作 -->
        <div class="col-lg-4">
          <div class="card border border-1 shadow-sm p-4 h-100">
            <h2 class="h6 fw-bold mb-3">承認ルート</h2>

            <ul class="list-group mb-4">
              <?php foreach ($route as $r): ?>
              <li class="list-group-item <?= $r['row_class'] ?>">
                <div class="d-flex justify-content-between">
                  <span><?= (int)$r['step_order'] ?></span>  //ここから作業開始！！！！！！！！！！！！！！！！
                  <span class="badge bg-warning text-dark">承認待ち</span>
                </div>
              </li>

              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <span>2. 開発 部長 ○○</span>
                  <span class="badge bg-light text-dark border">未到達</span>
                </div>
              </li>

              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <span>3. 副社長 ○○</span>
                  <span class="badge bg-light text-dark border">未到達</span>
                </div>
              </li>

              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <span>4. 社長 ○○</span>
                  <span class="badge bg-light text-dark border">未到達</span>
                </div>
              </li>
            </ul>

            <h2 class="h6 fw-bold mb-2">承認操作</h2>
            <form action="approval_action.php" method="POST">
              <input type="hidden" name="ringi_id" value="1">
              <label for="comment" class="form-label small">コメント(差し戻し・却下の場合は必須)</label>
              <textarea id="comment" name="comment" class="form-control mb-3" rows="3"></textarea>

              <div class="d-grid gap-2">
                <button type="submit" name="action" value="approve" class="btn btn-success fw-bold">承認</button>
                <button type="submit" name="action" value="return" class="btn btn-outline-secondary">差し戻し</button>
                <button type="submit" name="action" value="reject" class="btn btn-outline-danger">却下</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </main>
  </body>
</html> 

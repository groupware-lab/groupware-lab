<?php
  require_once __DIR__ . '/../common/auth_check.php';
  require_once __DIR__ . '/../common/db.php';
  require_once __DIR__ . '/../common/get_user_info.php';
  require_once __DIR__ . '/../common/ringi_status.php';

  //承認完了後のメッセージ表記
  $flash = $_SESSION['flash_message'] ?? '';
  unset($_SESSION['flash_message']);

  //ログインしている人のID
  $loginUserId = (int)$_SESSION['user_id'];

  //URLから id を受け取る(なければ空文字)
  $idRaw = $_GET['id'] ?? '';

  //数字だけでできているか確認→違えば一覧へ戻す
  if(!is_string($idRaw) || !ctype_digit($idRaw)) {
    header('Location: yuukyuu_list.php');
    exit;
  }

  //数値に変換して使う
  $yuukyuuId = (int)$idRaw;

  //稟議の申請内容を取得する用
  $sql = "SELECT yuukyuu.id,
                 yuukyuu.applicant_id, 
                 yuukyuu.leave_type, 
                 yuukyuu.start_date,
                 yuukyuu.end_date,
                 yuukyuu.days,
                 yuukyuu.status,
                 yuukyuu.current_step, 
                 yuukyuu.created_at,
                 users.name AS applicant_name,
                 users.department AS applicant_department,
                 users.position AS applicant_position
          FROM yuukyuu
            INNER JOIN users ON users.id = yuukyuu.applicant_id
          WHERE yuukyuu.id = :yuukyuu_id";

  $stmt = $connection->prepare($sql);
  $stmt->bindValue(':yuukyuu_id', $yuukyuuId, PDO::PARAM_INT);
  $stmt->execute();
  $yuukyuu = $stmt->fetch(PDO::FETCH_ASSOC);

  //存在しない稟議なら一覧へ戻す
  if($yuukyuu === false) {
    header('Location: yuukyuu_list.php');
    exit;
  }

  //承認ルートを取得する用
  $sql = "SELECT yuukyuu_approvers.step_order,
                 yuukyuu_approvers.approver_id,
                 yuukyuu_approvers.status,
                 yuukyuu_approvers.comment,
                 yuukyuu_approvers.approved_at,
                 users.name AS approver_name,
                 users.department AS approver_department,
                 users.position AS approver_position
          FROM yuukyuu_approvers
            INNER JOIN users ON users.id =  yuukyuu_approvers.approver_id
          WHERE yuukyuu_approvers.yuukyuu_id = :yuukyuu_id
          ORDER BY yuukyuu_approvers.step_order ASC";

  $stmt = $connection->prepare($sql);
  $stmt->bindValue(':yuukyuu_id',$yuukyuuId, PDO::PARAM_INT);
  $stmt->execute();
  $approvers =$stmt->fetchAll(PDO::FETCH_ASSOC);

  //-----アクセス制御---権限系の確認-------　申請者本人か確認 
  $isApplicant = ((int) $yuukyuu['applicant_id'] === $loginUserId);

  //承認ルートに入っているか
  $isInRoute = false;
  $canOperate =false;
  foreach($approvers as $a) {
    if((int)$a['approver_id'] === $loginUserId) {
      $isInRoute = true;
     if ((int)$a['step_order'] === (int)$yuukyuu['current_step']
          && $a['status'] === 'waiting'
          && $yuukyuu['status'] === 'pending') {
        $canOperate = true;
      }
    }
  }
  //申請者でも承認者でもない人は見られない
    if (!$isApplicant && !$isInRoute) {
      header('Location: yuukyuu_list.php');
      exit;
    }

  //戻り先(申請者はワークフロー画面、承認者は承認待ち一覧)
  $backUrl   = $isApplicant ? '../workflow/workflow_main.php' : 'yuukyuu_list.php';
  $backLabel = $isApplicant ? '← ワークフローに戻る' : '← 承認待ち一覧に戻る';


  //CSRFトークンを生成
  if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }

  //稟議全体のステータス表示
  $ringiStatus = ringiStatusLabel($yuukyuu['status']);

  //承認ルートの各行に、表示用のラベル・色を付け足す(共通関数を使う)
  $route = [];
  foreach ($approvers as $a) {
    $route[] = approvalRouteLabel($a, (int)$yuukyuu['current_step'], $yuukyuu['status']);
  }
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>有給申請 詳細</title>
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../common/common.css" rel="stylesheet">
  </head>

  <body class="app-page d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../common/header.php'; ?>

    <main class="container-fluid px-4 flex-grow-1">
      <?php if($flash !== ''): ?>
        <div class="alert alert-info mt-3"><?= htmlspecialchars($flash, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>
      <div class="row g-3 mt-1">

        <!-- 有給申請の内容 -->
        <div class="col-lg-8">
          <div class="card border border-1 shadow-sm p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h2 class="h6 fw-bold mb-0">有給申請 <?= sprintf('Y-%04d', $yuukyuu['id']) ?></h2>
              <span class="badge <?= $ringiStatus['class'] ?>">
                <?= htmlspecialchars($ringiStatus['label'], ENT_QUOTES, 'UTF-8') ?>
              </span>
            </div>

            <table class="table table-bordered mb-4">
              <tbody>
                <tr>
                  <th class="table-light" style="width: 25%;">申請日</th>
                  <td><?= date('Y/m/d', strtotime($yuukyuu['created_at'])) ?></td>
                </tr>
                <tr>
                  <th class="table-light">申請者</th>
                  <td><?= htmlspecialchars(trim($yuukyuu['applicant_department'] . ' ' . $yuukyuu['applicant_position'] . ' ' . $yuukyuu['applicant_name']), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                  <th class="table-light">休暇種別</th>
                  <td><?= htmlspecialchars($yuukyuu['leave_type'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <tr>
                  <th class="table-light">取得期間</th>
                  <td><?= date('Y/m/d', strtotime($yuukyuu['start_date'])) ?>～<?= date('Y/m/d', strtotime($yuukyuu['end_date'])) ?></td>
                </tr>
                <tr>
                  <th class="table-light">取得日数</th>
                  <td><?= htmlspecialchars((string)$yuukyuu['days'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
              </tbody>
            </table>
            <!-- 理由の列名を確認したら、ここに「理由」を足す -->
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
                  <span><?= (int)$r['step_order'] ?>.
                    <?= htmlspecialchars(trim($r['approver_department'] . ' ' . $r['approver_position'] . ' ' . $r['approver_name']), ENT_QUOTES, 'UTF-8')?>
                    <?php if((int)$r['approver_id'] === $loginUserId): ?>
                      <span class="small text-muted">(あなた)</span>
                    <?php endif; ?>
                  </span>
                  <span class="badge <?= $r['badge']?>"><?= $r['label'] ?></span>
                </div>

                <?php if(!empty($r['comment'])): ?>
                  <div class="small mt-1">「<?= htmlspecialchars($r['comment'], ENT_QUOTES, 'UTF-8') ?>」</div>
                <?php endif; ?>

                <?php if(!empty($r['approved_at'])):?>
                  <div class="small text-muted"><?= date('Y/m/d H:i', strtotime($r['approved_at'])) ?></div>
                <?php endif;?>
              </li>
              <?php endforeach; ?>
            </ul>
    
            <?php if($canOperate): ?>
            <h2 class="h6 fw-bold mb-2">承認操作</h2>
            <form action="yuukyuu_action.php" method="POST">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
              <input type="hidden" name="yuukyuu_id" value="<?= (int)$yuukyuu['id'] ?>">
              <label for="comment" class="form-label small">コメント(差し戻し・却下の場合は必須)</label>
              <textarea id="comment" name="comment" class="form-control mb-3" rows="3"></textarea>

              <div class="d-grid gap-2">
                <button type="submit" name="action" value="approve" class="btn btn-success fw-bold">承認</button>
                <button type="submit" name="action" value="return" class="btn btn-outline-secondary">差し戻し</button>
                <button type="submit" name="action" value="reject" class="btn btn-outline-danger">却下</button>
              </div>
            </form>
            <?php elseif($isInRoute): ?>
              <p class="small text-muted mb-0">現在、あなたが操作する段階ではありません。</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </main>
  </body>
</html> 

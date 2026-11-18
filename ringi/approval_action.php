<?php
require_once __DIR__ . '/../common/auth_check.php';
require_once __DIR__ . '/../common/db.php';

//POST以外のアクセスを禁止(URL直打ち対策)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: approval_list.php');
  exit;
}

//CSRFトークンの確認
if (
  !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
  !is_string($_POST['csrf_token']) ||
  !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
  http_response_code(400);
  exit('不正なリクエストです。画面を再度読み込んでから送信してください。');
}

//ログインしている人のID
$loginUserId = (int)$_SESSION['user_id'];

//入力値の受け取り 　稟議ID・承認判断（承認か却下か差し戻し）・コメント
$ringiIdRaw = $_POST['ringi_id'] ?? '';
$action     = $_POST['action'] ?? '';
$comment    = trim($_POST['comment'] ?? '');

//稟議IDが数字か確認
if (!is_string($ringiIdRaw) || !ctype_digit($ringiIdRaw)) {
  header('Location: approval_list.php');                                                                                                                                                        
  exit;
}
$ringiId = (int)$ringiIdRaw;

//一時的に差し戻しボタンを無効にしとく 実装したらこれは削除
if($action === 'return') {
  $_SESSION['flash_message'] = '差し戻しは現在準備中です。';
  header('Location: approval_detail.php?id=' . $ringiId);
  exit;
}

//バリデーションチェック：承認コメントの文字数チェック
if (mb_strlen($comment) > 255) {
  $_SESSION['flash_message'] = 'コメントは255文字以内で入力してください。';
  header('Location: approval_detail.php?id=' . $ringiId);
  exit;
}

//バリデーションチェック：却下のときにコメントを必須にする（空ならエラー表示）
if($action === 'reject' && $comment === '') {
  $_SESSION['flash_message'] = '却下の場合はコメント必須です。入力してください。';
  header('Location: approval_detail.php?id=' . $ringiId);
  exit;
}

try {
  $connection->beginTransaction();

  //① 稟議本体を取得(FOR UPDATE:処理が終わるまで他の人が同じ行を更新できないようにする) 
  $stmt = $connection->prepare(
    "SELECT id, status, current_step
       FROM ringi
      WHERE id = :ringi_id
      FOR UPDATE"
  );
  $stmt->bindValue(':ringi_id', $ringiId, PDO::PARAM_INT);
  $stmt->execute();
  $ringi = $stmt->fetch(PDO::FETCH_ASSOC);

  //② ログイン者が「今の承認者」で、まだ処理していないか確認(本当の守り)
  $isMyTurn = false;
  if ($ringi !== false && $ringi['status'] === 'pending') {
    $stmt = $connection->prepare(
      "SELECT status
         FROM ringi_approvers
        WHERE ringi_id    = :ringi_id
          AND approver_id = :me
          AND step_order  = :step
        FOR UPDATE"
    );
    $stmt->bindValue(':ringi_id', $ringiId, PDO::PARAM_INT);
    $stmt->bindValue(':me', $loginUserId, PDO::PARAM_INT);
    $stmt->bindValue(':step', (int)$ringi['current_step'], PDO::PARAM_INT);
    $stmt->execute();
    $myStatus = $stmt->fetchColumn();
    $isMyTurn = ($myStatus === 'waiting');
  }

  if (!$isMyTurn) {
    //二重送信・他人の稟議・すでに処理済みなどはここで止める
    $connection->rollBack();
    $_SESSION['flash_message'] = 'この稟議は承認できません(すでに処理済みか、あなたの順番ではありません)。';
    header('Location: approval_list.php');
    exit;
  }

  //自分（承認者）の行のステータスを更新
  if($action === 'reject') {
    $newStatus = 'rejected';
  } else {
    $newStatus = 'approved';
  }

  $stmt = $connection->prepare(
    "UPDATE ringi_approvers
        SET status      = :status,
            comment     = :comment,
            approved_at = NOW()
      WHERE ringi_id    = :ringi_id
        AND approver_id = :me
        AND step_order  = :step"
  );
  $stmt->bindValue(':status', $newStatus, PDO::PARAM_STR);
  $stmt->bindValue(':comment', $comment === '' ? null : $comment, $comment === '' ? PDO::PARAM_NULL : PDO::PARAM_STR);
  $stmt->bindValue(':ringi_id', $ringiId, PDO::PARAM_INT);
  $stmt->bindValue(':me', $loginUserId, PDO::PARAM_INT);
  $stmt->bindValue(':step', (int)$ringi['current_step'], PDO::PARAM_INT);
  $stmt->execute();

  //④ 承認者が全部で何段あるか数える
  $stmt = $connection->prepare(
    "SELECT MAX(step_order) FROM ringi_approvers WHERE ringi_id = :ringi_id"
  );
  $stmt->bindValue(':ringi_id', $ringiId, PDO::PARAM_INT);
  $stmt->execute();
  $lastStep = (int)$stmt->fetchColumn();

  //⑤ 稟議本体を更新
  if($action === 'reject') {
    $stmt = $connection->prepare("UPDATE ringi SET status = 'rejected' WHERE id =:ringi_id");
    $message = '申請を却下しました。';
  } elseif ((int)$ringi['current_step'] >= $lastStep) {
    //最後の承認者 → 稟議全体を承認済みにする
    $stmt = $connection->prepare("UPDATE ringi SET status = 'approved' WHERE id = :ringi_id");
    $message = '承認しました。すべての承認が完了しました。';
  } else {
    //まだ次の承認者がいる → 次の段階へ回す
    $stmt = $connection->prepare("UPDATE ringi SET current_step = current_step + 1 WHERE id = :ringi_id");
    $message = '承認しました。次の承認者に送信しました。';
  }
  $stmt->bindValue(':ringi_id', $ringiId, PDO::PARAM_INT);
  $stmt->execute();

  $connection->commit();

} catch (PDOException $e) {
  if ($connection->inTransaction()) {
    $connection->rollBack();
  }

  error_log('[approval_action] ' . $e->getMessage()); //詳細はサーバーのログだけに残す
  $_SESSION['flash_message'] = '承認処理中にエラーが発生しました。時間をおいて再度お試しください。';
  header('Location: approval_detail.php?id=' . $ringiId);
  exit;
}

//完了したら承認待ち一覧へ(PRGパターン:F5で再送信されない)
$_SESSION['flash_message'] = $message;
header('Location: approval_list.php');
exit;
?>
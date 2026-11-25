<?php
require_once __DIR__ . '/../common/auth_check.php';  //セッションチェック
require_once __DIR__ . '/../common/db.php';          //DB接続

//POST以外のアクセスを禁止(URL直打ち対策)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: yuukyuu_list.php');
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

//入力値の受け取り 　有給ID・承認判断（承認か却下か差し戻し）・コメント
$yuukyuuIdRaw = $_POST['yuukyuu_id'] ?? '';
$action     = $_POST['action'] ?? '';
$comment    = trim($_POST['comment'] ?? '');

//有給IDが数字だけの文字列か判定（文字列・0～9でできているか）
if (!is_string($yuukyuuIdRaw) || !ctype_digit($yuukyuuIdRaw)) {
  header('Location: yuukyuu_list.php');    
  exit;
}
$yuukyuuId = (int)$yuukyuuIdRaw;

//一時的に差し戻しボタンを無効にしとく 実装したらこれは削除
if($action === 'return') {
  $_SESSION['flash_message'] = '差し戻しは現在準備中です。';
  header('Location: yuukyuu_detail.php?id=' . $yuukyuuId);
  exit;
}

//バリデーションチェック：承認コメントの文字数チェック
if (mb_strlen($comment) > 255) {
  $_SESSION['flash_message'] = 'コメントは255文字以内で入力してください。';
  header('Location: yuukyuu_detail.php?id=' . $yuukyuuId);
  exit;
}

//バリデーションチェック：却下のときにコメントを必須にする（空ならエラー表示）
if($action === 'reject' && $comment === '') {
  $_SESSION['flash_message'] = '却下の場合はコメント必須です。入力してください。';
  header('Location: yuukyuu_detail.php?id=' . $yuukyuuId);
  exit;
}

try {
  $connection->beginTransaction();
  //稟議本体を取得（forupdateで処理が終わるまで他の人が同じ行を更新できないように設定（排他制御））
  $stmt = $connection->prepare(
    "SELECT id, status, current_step
     FROM yuukyuu
     WHERE id = :yuukyuu_id
     FOR UPDATE"
  );
  $stmt->bindValue(':yuukyuu_id', $yuukyuuId, PDO::PARAM_INT);
  $stmt->execute();
  $yuukyuu = $stmt->fetch(PDO::FETCH_ASSOC);

  //ログイン者が今の承認者、まだ処理していないか
  $isMyTurn = false;
  if($yuukyuu !== false && $yuukyuu['status'] === 'pending') {
    $stmt = $connection->prepare(
      "SELECT status
       FROM yuukyuu_approvers
       WHERE yuukyuu_id = :yuukyuu_id
       AND approver_id = :me
       AND step_order = :step
       FOR UPDATE"
    );
    $stmt->bindValue(':yuukyuu_id', $yuukyuuId, PDO::PARAM_INT);
    $stmt->bindValue(':me', $loginUserId, PDO::PARAM_INT);
    $stmt->bindValue(':step', (int)$yuukyuu['current_step'], PDO::PARAM_INT);
    $stmt->execute();
    $mystatus = $stmt->fetchColumn();
    $isMyTurn = ($mystatus === 'waiting');
  }

  if(!$isMyTurn) {
    //二重送信・他人の稟議・すでに処理済みなどの場合はここでストップ
    $connection->rollBack();
    $_SESSION['flash_message'] = 'この有給申請は承認できません。（既に処理済みか、あなたの順番ではありません。)';
    header('Location: yuukyuu_list.php');
    exit;
  }

  //自分（承認者）の行のステータスを更新
  if($action === 'reject') {
    $newStatus = 'rejected';
  } elseif ($action === 'return') {
    $newStatus = 'returned';
  } else {
    $newStatus = 'approved';
  }

  $stmt = $connection->prepare(
    "UPDATE yuukyuu_approvers
    SET status =:status,
        comment =:comment,
        approved_at = NOW()
    WHERE yuukyuu_id = :yuukyuu_id
    AND approver_id = :me
    AND step_order = :step"
  );
  $stmt->bindValue(':status', $newStatus, PDO::PARAM_STR);
  $stmt->bindValue(':comment',
    $comment === '' ? null : $comment,
    $comment === '' ? PDO::PARAM_NULL : PDO::PARAM_STR
  );
  $stmt->bindValue(':yuukyuu_id', $yuukyuuId, PDO::PARAM_INT);
  $stmt->bindValue(':me', $loginUserId, PDO::PARAM_INT);
  $stmt->bindValue(':step', (int)$yuukyuu['current_step'], PDO::PARAM_INT);
  $stmt->execute();

  //承認者が全部で何段あるか
  $stmt = $connection->prepare(
    "SELECT MAX(step_order) FROM yuukyuu_approvers WHERE yuukyuu_id = :yuukyuu_id"
  );
  $stmt->bindValue(':yuukyuu_id', $yuukyuuId, PDO::PARAM_INT);
  $stmt->execute();
  $lastStep = (int)$stmt->fetchColumn();

  //有給申請本体の更新
  if($action === 'reject') {
    $stmt = $connection->prepare(
      "UPDATE yuukyuu 
       SET status ='rejected'
       WHERE id = :yuukyuu_id"
      );
    $message = '申請を却下しました。';
  } elseif((int)$yuukyuu['current_step'] >= $lastStep) {
    //最後の承認者　→　承認者全体を承認済みにする
    $stmt = $connection->prepare(
      "UPDATE yuukyuu
       SET status = 'approved'
       WHERE id = :yuukyuu_id");
    $message = '承認しました。すべての承認が完了しました。';
  } else {
    //まだ次の承認者がいる　→次の段階へ回す
    $stmt = $connection->prepare(
      "UPDATE yuukyuu
       SET current_step = current_step + 1
       WHERE id = :yuukyuu_id"
    );
    $message = '承認しました。次の承認者に送信しました。';
  }
  $stmt->bindValue(':yuukyuu_id', $yuukyuuId, PDO::PARAM_INT);
  $stmt->execute();

  $connection->commit();
} catch (PDOException $e) {
  if($connection->inTransaction()) {
    $connection->rollBack();
  }

  //エラーの詳細情報はサーバーのログだけに残す
  error_log('[yuukyuu_action]' . $e->getMessage());
  $_SESSION['flash_message'] = '承認処理中にエラーが発生しました。再度時間をおいてからお試しください。';
  header('Location: yuukyuu_detail.php?id=' . $yuukyuuId);
  exit;
}

//完了したら承認待ち一覧へ遷移
$_SESSION['flash_message'] = $message;
header('Location: yuukyuu_list.php');
exit;
?>
<?php
  require_once __DIR__ . '/../common/auth_check.php';

  //POST通信以外のアクセス不可（URL直打ち対策）
  if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ringi.php');
    exit;
  }

  //罠のページから送られてきて、フォームにトークン項目がない（POST側が無い）
  //セッションが切れていたり、ringi.php を一度も開かずに直接送信した（セッション側が無い）
  if(
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
  ) {
    http_response_code(400);
    exit('不正なリクエストです。画面を再度読み込んでから送信してください。');
  }

  require_once __DIR__ . '/../common/db.php';
  $applicantId =(int)$_SESSION['user_id'];

  //入力値の取得（前後に空白を除去する--全角スペースは対象外なので注意）
  $title = trim($_POST['title'] ?? '');
  $content = trim($_POST['content' ?? '']);
  $amountRow = trim($_POST['amount' ?? '']);
  $approverIds = $_POST['approver_ids'] ?? [];

  //カンマ付きで入力された金額があればなしに変換
  $amountRow = str_replace([',', ', '], '', $amountRow);

  $erros = [];

  //稟議内容のバリデーションチェック
  if($title === '') {
    $errors[] = '件名を入力してください。';
  } elseif (mb_strlen($title) > 100) {
    $errors[] = '件名は100文字以内で入力してください。';
  }

  if($content === '') {
    $errors[] = '稟議内容を入力してください。';
  }

  //引数が0～9の数値か
  if($amountRow === '' || !ctype_digit($amountRow)) {
    $errors[] = '費用金額は0以上の整数で入力してください。';
  }

  $amout = (int)$amountRow;

  //承認者者選択のバリデーションチェック
  //承認者が配列であること、空欄を除く、数値でないこと
  if(!is_array($approverIds)) {
    $approverIds = [];
  }
  $approverIds = array_values(array_filter(array_map('trim', array_filter($approverIds, 'is_string')),fn($v) => $v !==''));

  if(count($approverIds) === 0) {
    $errors = '承認者を１人以上選択してください。';
  } else {
    foreach($approverIds as $id) {   //ここあとで意味調べる
      if(!ctype_digit($id)) {
        $errors[] = '承認者の指定が不正です。';
        break;
      }
    }
    $approverIds =array_map('intbal', $approverIds);
  }

  if(count($approverIds) !== count(array_unique($approverIds))) {
    $errors = '同じ承認者が重複して選択されています。';
  }

  if(in_array($applicantId, $approverIds, true)) {
    $errors = '申請者本人を承認者に指定することはできません。';
  }

// 承認者が実在し、かつ承認できる職位(リーダー以上)かDBと照合して確認（セキュリティ対策）
// 開発者ツールでvalue値を書き換えるとなりすまして送信できちゃうのを防ぐらしい
if(empty($errors)) {
  $approverPositions = ['リーダー', '課長', '部長', '副社長', '社長'];
  $idPlaceholders = implode(',', array_fill(0, count($approverIds), '?'));
  $posPlaceholders = implode(',', array_fill(0, count($approverPositions), '?'));
  $stmt = $connection->prepare(
    "SELECT count(*) FROM users
    WHERE id = IN ($idPlaceholders)
    AND position IN ($posPlaceholders)"
  );
  $stmt->execute([...$approverIds, ...$approverPositions]);
  if((int)$stmt->fetchColumn() !== count($approverIds)) {
    $errors[] = '承認者に指定できない社員が含まれています。';
  }
}

//エラーがあっても入力内容を保持したままringi.phpに戻るように
if(!empty($errors)) {
  $_SESSION['ringi_errors'] = $errors;
  $_SESSION['ringi_old'] = [
    'title'=> $title, 
    'content'=> $content,
    'amount' => $amountRow,
    'approver_ids' =>$approverIds,
  ];
  header('Location: ringi.php');
  exit;
}
?>
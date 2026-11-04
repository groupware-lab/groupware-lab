<?php   //有給休暇申請 送信処理（記入画面のフォームの送信先。入力チェック → DBに保存）
require_once __DIR__ . '/../common/auth_check.php';
require_once __DIR__ . '/yuukyuu_common.php';

// POST通信以外のアクセス不可（URL直打ち対策）
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: yuukyuu.php');
    exit;
}

// CSRFトークンの照合（フォームにトークンが無い／セッションが切れている場合は弾く）
if (
    !isset($_POST['csrf_token'], $_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(400);
    exit('不正なリクエストです。画面を再度読み込んでから送信してください。');
}

require_once __DIR__ . '/../common/db.php';
$applicantId = (int)$_SESSION['user_id'];   // 申請者は、フォームの値ではなくログイン中のセッションから取る

// 入力値の取得（文字列以外が送られてきたら空として扱う。前後の空白は除去）
$post = fn(string $key): string => is_string($_POST[$key] ?? null) ? trim($_POST[$key]) : '';

$leaveType = $post('leave_type');
$startDate = $post('start_date');
$endDate   = $post('end_date');
$daysRaw   = $post('days');
$reason    = $post('reason');

// 承認者（配列で受け取り、文字列以外と空欄を除く。選んだ順＝承認順）
$approverIds = $_POST['approver_ids'] ?? [];
if (!is_array($approverIds)) {
    $approverIds = [];
}
$approverIds = array_values(array_filter(
    array_map('trim', array_filter($approverIds, 'is_string')),
    fn($v) => $v !== ''
));

$errors = [];

// ---- 休暇の種類（4種類のどれか）----
if (!in_array($leaveType, YUUKYUU_LEAVE_TYPES, true)) {
    $errors[] = '休暇の種類を選択してください。';
}

// ---- 日付（YYYY-MM-DD の実在する日付か）----
$start = DateTime::createFromFormat('!Y-m-d', $startDate);
$end   = DateTime::createFromFormat('!Y-m-d', $endDate);
$startOk = ($start !== false) && $start->format('Y-m-d') === $startDate;
$endOk   = ($end !== false) && $end->format('Y-m-d') === $endDate;

if (!$startOk) {
    $errors[] = '開始日を正しく入力してください。';
}
if (!$endOk) {
    $errors[] = '終了日を正しく入力してください。';
}
if ($startOk && $endOk && $end < $start) {
    $errors[] = '終了日は開始日以降の日付にしてください。';
}

// ---- 取得日数（0.5日単位、小数第1位まで。DECIMAL(3,1)に収まる範囲）----
$days = 0.0;
$daysOk = false;
if (!preg_match('/^\d{1,2}(\.\d)?$/', $daysRaw)) {
    $errors[] = '取得日数は0.5日単位の数値で入力してください。';
} else {
    $days = (float)$daysRaw;
    if ($days < 0.5 || fmod($days * 2, 1.0) !== 0.0) {
        $errors[] = '取得日数は0.5日単位（0.5以上）で入力してください。';
    } else {
        $daysOk = true;
    }
}

// ---- 日付と日数・休暇の種類の整合性 ----
if ($daysOk && $startOk && $endOk && $end >= $start) {
    $span = $start->diff($end)->days + 1;   // 開始日〜終了日の日数（両端を含む）
    if ($days > $span) {
        $errors[] = '取得日数が、開始日〜終了日の期間（' . $span . '日間）を超えています。';
    }
}
if ($leaveType === YUUKYUU_TYPE_HALF) {
    if ($daysOk && $days !== 0.5) {
        $errors[] = '有給(半休)の取得日数は0.5日にしてください。';
    }
    if ($startOk && $endOk && $startDate !== $endDate) {
        $errors[] = '有給(半休)は、開始日と終了日を同じ日にしてください。';
    }
}
if ($leaveType === YUUKYUU_TYPE_FULL && $daysOk && fmod($days, 1.0) !== 0.0) {
    $errors[] = '有給(全休)の取得日数は、1日単位で入力してください。';
}

// ---- 申請理由（任意。入れる場合は255文字以内）----
if (mb_strlen($reason) > 255) {
    $errors[] = '申請理由は255文字以内で入力してください。';
}

// ---- 承認者（1人以上、数値、重複なし、申請者本人は不可）----
$approverInts = [];
if (count($approverIds) === 0) {
    $errors[] = '承認者を1人以上選択してください。';
} elseif (count(array_filter($approverIds, 'ctype_digit')) !== count($approverIds)) {
    $errors[] = '承認者の指定が不正です。';
} else {
    $approverInts = array_map('intval', $approverIds);
    if (count($approverInts) !== count(array_unique($approverInts))) {
        $errors[] = '同じ承認者が重複して選択されています。';
    }
    if (in_array($applicantId, $approverInts, true)) {
        $errors[] = '申請者本人を承認者に指定することはできません。';
    }
}

// 承認者が実在し、かつ承認できる職位かをDBで照合（画面の値を書き換えての不正送信を防ぐ）
if (empty($errors)) {
    $positions = yuukyuuApproverPositions();
    $idPh  = implode(',', array_fill(0, count($approverInts), '?'));
    $posPh = implode(',', array_fill(0, count($positions), '?'));
    $stmt = $connection->prepare(
        "SELECT COUNT(*) FROM users
         WHERE id IN ($idPh)
         AND position IN ($posPh)"
    );
    $stmt->execute([...$approverInts, ...$positions]);
    if ((int)$stmt->fetchColumn() !== count($approverInts)) {
        $errors[] = '承認者に指定できない社員が含まれています。';
    }
}

// エラーがあったら、入力内容を保持したまま記入画面に戻す
$oldInput = [
    'leave_type'   => $leaveType,
    'start_date'   => $startDate,
    'end_date'     => $endDate,
    'days'         => $daysRaw,
    'reason'       => $reason,
    'approver_ids' => $approverIds,
];
if (!empty($errors)) {
    $_SESSION['yuukyuu_errors'] = $errors;
    $_SESSION['yuukyuu_old']    = $oldInput;
    header('Location: yuukyuu.php');
    exit;
}

// ---- DBに保存（yuukyuu と yuukyuu_approvers は2つセットで保存する）----
try {
    $connection->beginTransaction();

    // 1. 申請内容を yuukyuu に記録（status は承認待ち。approver_id と approved_at は使わずNULLのまま）
    $stmt = $connection->prepare(
        "INSERT INTO yuukyuu (applicant_id, leave_type, start_date, end_date, days, reason, status)
         VALUES (?, ?, ?, ?, ?, ?, 'pending')"
    );
    $stmt->execute([
        $applicantId,
        $leaveType,
        $startDate,
        $endDate,
        number_format($days, 1, '.', ''),     // 例: 0.5 / 1.0 / 2.5
        ($reason === '') ? null : $reason,    // 任意項目なので、空ならNULL
    ]);

    // いま登録した yuukyuu の id を取得
    $yuukyuuId = (int)$connection->lastInsertId();

    // 2. 承認者を1人1行で yuukyuu_approvers に記録（step_order = 選んだ順番）
    $stmt = $connection->prepare(
        "INSERT INTO yuukyuu_approvers (yuukyuu_id, approver_id, step_order)
         VALUES (?, ?, ?)"
    );
    foreach ($approverInts as $index => $approverId) {
        $stmt->execute([$yuukyuuId, $approverId, $index + 1]);
    }

    $connection->commit();
} catch (PDOException $e) {
    $connection->rollBack();
    error_log('有給申請登録エラー：' . $e->getMessage());
    $_SESSION['yuukyuu_errors'] = ['システムエラーが発生しました。しばらくしてから再度お試しください。'];
    $_SESSION['yuukyuu_old']    = $oldInput;
    header('Location: yuukyuu.php');
    exit;
}

// 保存できたら、使い終わったCSRFトークンを破棄して（二重送信を防ぐ）、完了メッセージつきで記入画面へ
unset($_SESSION['csrf_token']);
header('Location: yuukyuu.php?done=1');   // 申請後の遷移先を変えたい場合は、ここを変更する
exit;

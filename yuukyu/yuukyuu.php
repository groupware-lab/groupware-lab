<?php //有給休暇申請 記入画面（申請者が入力する画面）のPHP部分
require_once __DIR__ . '/../common/auth_check.php';
require_once __DIR__ . '/../common/get_user_info.php';   // $loginUser（部署・職位・氏名）を用意する
require_once __DIR__ . '/../common/db.php';
require_once __DIR__ . '/yuukyuu_common.php';

//csrfトークンを生成（フォームに埋め込み、送信時に yuukyuu_submit.php で照合する）
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrfToken = $_SESSION['csrf_token'];

// 送信処理でエラーがあったときの「エラー文」と「前回の入力内容」（一度表示したら消す）
$errors = $_SESSION['yuukyuu_errors'] ?? [];
$old    = $_SESSION['yuukyuu_old'] ?? [];
unset($_SESSION['yuukyuu_errors'], $_SESSION['yuukyuu_old']);

// 入力欄の初期値（エラーで戻ってきたときは前回の入力、初回は空）
$form = [
    'leave_type' => $old['leave_type'] ?? '',
    'start_date' => $old['start_date'] ?? '',
    'end_date'   => $old['end_date'] ?? '',
    'days'       => $old['days'] ?? '',
    'reason'     => $old['reason'] ?? '',
];

// 承認者の選択欄（エラーで戻ってきたときは前回選んだ人数ぶん。初回は1行）
// 各要素は、その行で選んでいた承認者のid（未選択は空文字）
$selectedApprovers = (isset($old['approver_ids']) && is_array($old['approver_ids']) && count($old['approver_ids']) > 0)
    ? array_values($old['approver_ids'])
    : [''];

$leaveTypes  = YUUKYUU_LEAVE_TYPES;                                        // 休暇の種類のプルダウン用
$todayText   = date('Y年n月j日');                                          // 申請日
$doneMessage = !empty($_GET['done']) ? '有給休暇を申請しました。' : null;   // 申請完了のメッセージ（無ければnull）

// 承認者候補：承認できる職位のみ、申請者本人は除外（自分の部署を先頭に並べる）
$positions = yuukyuuApproverPositions();

$params = [
    'my_dept' => $loginUser['department'],
    'me'      => $_SESSION['user_id'],
];
$inPh  = [];
$ordPh = [];
foreach ($positions as $i => $p) {
    $inPh[]  = ":in{$i}";
    $ordPh[] = ":ord{$i}";
    $params["in{$i}"]  = $p;
    $params["ord{$i}"] = $p;
}

$sql = "SELECT id, department, position, name FROM users
        WHERE position IN (" . implode(',', $inPh) . ")
        AND id <> :me
        ORDER BY
            CASE WHEN department = :my_dept THEN 0 ELSE 1 END,
            department,
            FIELD(position, " . implode(',', $ordPh) . "),
            name";

$stmt = $connection->prepare($sql);
$stmt->execute($params);
$approverCandidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>有給休暇申請画面</title>

    <!-- 背景色のデザインの共通部分を読み込み -->
    <link href="../common/common.css" rel="stylesheet">

    <!-- bootstrapのcssを読み込む用 -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- 有給用のCSSの読み込み -->
    <link rel="stylesheet" href="yuukyuu.css">
  </head>

  <body class="app-page d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../common/header.php'; ?>

    <form action="yuukyuu_submit.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
      <div class="container">      
        <div class="title">有給休暇申請</div>
            
        <!-- /*php申請エラーメッセ*/ -->
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0">
            <li></li>
          </ul>
        </div>
            
        <table>
          <tbody>
            <tr>
              <th>申請日</th>
              <td colspan="5"><?= htmlspecialchars($todayText, ENT_QUOTES, 'UTF-8') ?></td>
                        
              <th>休暇種別</th>
                <td colspan="">
                   <select name="leave_type">
                     <option value="">種別を選択してください</option>
                        <?php foreach($leaveTypes as $type): ?>
                          <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8')?>">
                            <?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8')?>
                          </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>

            <tr>
              <th>部署名</th>
                <td><?= htmlspecialchars($loginUser['department'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
              <th>職位</th>
                <td><?= htmlspecialchars($loginUser['position'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
              <th>申請者</th>
                <td><?= htmlspecialchars($loginUser['name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>

            <tr>
              <th>開始日</th>
              <td>
                <input type="date" name="start_date">
              </td>
              <th>終了日</th>
                <td>
                  <input type="date" name="end_date">
                </td>
              <th>取得日数</th>
                <td>
                  <input type="number" step="0.5" min="0.5" name="days">
                </td>
            </tr>
                    
            <tr>
              <th>申請理由（任意）</th>
                <td>
                  <textarea name="reason" placeholder="ここに詳細を記入"></textarea>
                </td>
            </tr>
          </tbody>
        </table>
          
        <div class="approver-section" id="approver-section">
          <div class="approver-box">
            <div class="approver-block" id="approver-list">
              <div class="approver-row">
                <span class="approver-label">承認者1：</span>
                <select name="approver_ids[]" class="approver-input approver-select" requiered>
                  <option value="">承認者を選択してください</option>
                  <?php foreach($approverCandidates as $u):?>
                    <option value="<?= htmlspecialchars($u['id'], ENT_QUOTES, 'UTF-8') ?>">
                      <?= htmlspecialchars($u['department'] . ' ' . $u['position'] . ' ' . $u['name'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                
                <button type="button" class="add-approver-btn">＋</button>  
              </div>
            </div>
          </div>
        </div>

        <div class="submit-row">
          <button type="submit" class="submit-btn">申請する</button>
        </div>
      </div>
    </form>
    <script src="../common/approver.js"></script>
  </body>
</html>

 <!-- ============================================================
// ここまでがバックエンド（PHPだけ）です。
// フロントを作るときは、このファイルの末尾にPHPの閉じタグを書いて、その続きにHTMLを書き足してください。
// HTMLに出すときは、必ず htmlspecialchars($値, ENT_QUOTES, 'UTF-8') で包みます（ringi.php と同じ書き方）。
//
// 【使える変数】
//   $loginUser['department'] / ['position'] / ['name']   部署名・職位・申請者（ログイン中の人。自動表示）
//   $todayText                申請日
//   $leaveTypes               休暇の種類（4つ）。プルダウンの選択肢として foreach で出す
//   $form['leave_type'] / ['start_date'] / ['end_date'] / ['days'] / ['reason']
//                             各入力欄の初期値（エラーで戻ってきたときに、前回の入力を復元する用）
//   $approverCandidates       承認者の候補。1人ごとに id / department / position / name が入った配列
//                             プルダウンに「部署 職位 氏名」の形で foreach で出す（value は id）
//   $selectedApprovers        承認者の選択欄の行ぶんの配列。前回選んだidが入っている（初回は空文字1つ）
//                             foreach で行を作り、候補のidと同じものを selected にして復元する
//   $errors                   エラー文の配列（空なら出さない）
//   $doneMessage              申請完了のメッセージ（nullなら出さない）
//   $csrfToken                CSRFトークン
//
// 【フォーム】
//   送信先: yuukyuu_submit.php   方式: POST
//   hidden   name="csrf_token"     値: $csrfToken
//   select   name="leave_type"     選択肢: $leaveTypes
//   date     name="start_date"     休暇開始日
//   date     name="end_date"       休暇終了日
//   number   name="days"           取得日数（0.5日単位。半休は0.5）
//   textarea name="reason"         申請理由（任意）
//   select   name="approver_ids[]" 承認者（行ごとに1つ。「＋」「－」で行を増減させる。選んだ順が承認順）
//
// 【フロント側で用意するもの】
//   承認者の行を増減させるJavaScript（「＋」で行を追加、「－」で削除、「承認者1」「承認者2」…の番号の振り直し）
// ============================================================ -->

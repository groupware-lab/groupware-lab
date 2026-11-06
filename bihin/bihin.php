<?php
require_once __DIR__ . '/../common/auth_check.php';        //セッションチェック
require_once __DIR__ . '/../common/get_user_info.php';     //loginユーザー表示
require_once __DIR__ . '/../common/db.php';                //各種共通部品
require_once __DIR__ . '/../common/approver.php';          //承認ルートの共通部品

//csrfトークンを生成
if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = $_SESSION['bihin_errors'] ?? [];
unset($_SESSION['bihin_errors']);

// 承認者候補:備品購入を承認できる職位のみ、申請者本人は除外
// （approver.php に 'bihin' の設定が無ければ、稟議('ringi')と同じ職位を使う）
$positions = APPROVER_POSITIONS['bihin'] ?? APPROVER_POSITIONS['ringi'];

$params = [
    'my_dept' => $loginUser['department'],
    'me' => $_SESSION['user_id'],
];
$inPh = [];
$ordPh = [];
foreach($positions as $i =>$p) {
    $inPh[] = ":in{$i}";
    $ordPh[] =":ord{$i}";
    $params["in{$i}"] = $p;
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>備品購入申請 申請画面</title>

    <!-- 背景色のデザインの共通部分を読み込み -->
    <link href="../common/common.css" rel="stylesheet">
    
    <!-- bootstrapのcssを読み込む用 -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- 備品購入用のCSSの読み込み（ringi.css + 小さいセル用の追加2ルール） -->
    <link rel="stylesheet" href="bihin.css">
</head>

<body class="app-page d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../common/header.php'; ?>  

    <form action="bihin_submit.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-container">
            <div class="title">備品購入申請書</div>
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">
                    <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <table>
                <tbody>
                    <tr>
                        <th>申請日</th>
                        <td colspan="5" id="today-date" style="font-weight: bold;"></td>
                    </tr>

                    <tr>
                        <th>品名</th>
                        <td colspan="5">
                            <input type="text" name="item_name" placeholder="品名を入力" maxlength="100" required>
                        </td>
                    </tr>

                    <tr>
                        <th>部署名</th>
                        <td>
                            <?= htmlspecialchars($loginUser['department'] ?? 'ー', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <th>職位</th>
                        <td>
                            <?= htmlspecialchars($loginUser['position'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <th>申請者</th>
                        <td>
                            <?=  htmlspecialchars($loginUser['name'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>

                    <tr>
                        <th>単価</th>
                        <td class="with-unit">
                            <input type="number" name="unit_price" min="0" required>　円
                        </td>
                        <th>数量</th>
                        <td>
                            <input type="number" name="quantity" min="1" required>
                        </td>
                        <th>合計</th>
                        <td id="total-price" style="font-weight: bold;">0 円</td>
                    </tr>

                    <tr>
                        <th colspan="6" class="text-center">購入理由・用途</th>
                    </tr>
                    <tr class="content-row">
                        <td colspan="6">
                            <textarea name="content" placeholder="ここに詳細を記入してください" required></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="approver-section" id="approver-section">
                <div class="approver-box">
                    <div class="approver-block" id="approver-list">

                        <div class="approver-row">
                            <span class="approver-label">承認者1：</span>
                            <select name="approver_ids[]" class="approver-input approver-select" required>
                                <option value="">承認者を選択してください</option>
                                <?php foreach ($approverCandidates as $u):?>
                                    <option value="<?= htmlspecialchars($u['id'], ENT_QUOTES, "UTF-8")?>">
                                        <?=  htmlspecialchars($u['department'] . ' ' . $u['position'] . ' ' . $u['name'], ENT_QUOTES, 'UTF-8') ?>
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

    <script>
        const today = new Date(); /* 今日の日付を取得 */
        const year = today.getFullYear();
        const month = today.getMonth() + 1;
        const date = today.getDate();
        document.getElementById('today-date').textContent = year + '年' + month + '月' + date + '日';

        //購入理由を入力された中身に合わせてtextareaの高さを調整
        const textarea = document.querySelector('textarea')

        function autoResize() {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        textarea.addEventListener('input', autoResize);
        window.addEventListener('resize', autoResize);
        autoResize();

        // 合計金額の自動計算（表示専用。保存時はサーバー側で再計算する）
        const unit = document.querySelector('[name="unit_price"]');
        const qty = document.querySelector('[name="quantity"]');
        const total = document.getElementById('total-price');

        function updateTotal() {
            const sum = (Number(unit.value) || 0) * (Number(qty.value) || 0);
            total.textContent = sum.toLocaleString() + ' 円';
        }

        unit.addEventListener('input', updateTotal);
        qty.addEventListener('input', updateTotal);

        const approverList = document.getElementById('approver-list');

        // 承認者の行を上から「承認者1、承認者2…」の順にラベルを振り直す
        function renumberApprovers() {
            document.querySelectorAll('.approver-row').forEach((row, index) => {
                row.querySelector('.approver-label').textContent = '承認者' + (index + 1) + '：';
            });
        }

        approverList.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-approver-btn')) {
                const firstRow = document.querySelector('.approver-row');
                const newRow = firstRow.cloneNode(true);

                // クローン元の選択状態を引き継がないようリセット
                newRow.querySelector('select').selectedIndex = 0;

                const btn = newRow.querySelector('button');
                btn.textContent = '－';
                btn.classList.remove('add-approver-btn');
                btn.classList.add('remove-approver-btn');

                approverList.appendChild(newRow);
                renumberApprovers();
                return;
            }

            if (e.target.classList.contains('remove-approver-btn')) {
                e.target.closest('.approver-row').remove();
                renumberApprovers();
            }
        });
    </script>
</body>
</html>

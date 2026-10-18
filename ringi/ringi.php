<?php
require_once __DIR__ . '/../common/auth_check.php';
require_once __DIR__ . '/../common/get_user_info.php';
require_once __DIR__ . '/../common/db.php';
require_once __DIR__ . '/../common/approver.php';

//csrfトークンを生成
if(empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = $_SESSION['ringi_errors'] ?? [];
unset($_SESSION['ringi_errors']);

// 承認者候補:稟議を承認できる職位のみ、申請者本人は除外
$positions = APPROVER_POSITIONS['ringi'];

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
    <title>稟議書 申請画面</title>

    <!-- 背景色のデザインの共通部分を読み込み -->
    <link href="../common/common.css" rel="stylesheet">
    
    <!-- bootstrapのcssを読み込む用 -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- 稟議用のCSSの読み込み -->
    <link rel="stylesheet" href="ringi.css">
</head>

<body class="bg-light d-flex flex-column min-vh-100">
    <?php require_once __DIR__ . '/../common/header.php'; ?>

    <form action="ringi_submit.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-container">
            <div class="title">稟議書</div>
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
                        <th>プロジェクト名</th>
                        <td colspan="5">
                            <input type="text" name="title" placeholder="プロジェクト名を入力">
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
                        <th>費用見積</th>
                        <td colspan="5">
                            <input type="number" name="amount">　円
                        </td>
                    </tr>

                    <tr>
                        <th colspan="6" class="text-center">稟議内容（主旨・理由・目的および期待する効果等）</th>
                    </tr>
                    <tr class="content-row">
                        <td colspan="6">
                            <textarea name="content" placeholder="ここに詳細を記入してください"></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="approver-section" id="approver-section">
                <div class="approver-box">
                    <div class="approver-block" id="approver-list">

                        <div class="approver-row">
                            <span class="approver-label">承認者1：</span>
                            <select name="approver_ids[]" class="approver-input approver-select" requered>
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

        //稟議内容を入力された中身に合わせてtextareaの高さを調整
        const textarea = document.querySelector('textarea')

        function autoResize() {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        textarea.addEventListener('input', autoResize);
        window.addEventListener('resize', autoResize);
        autoResize();

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
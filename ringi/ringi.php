<?php
require_once __DIR__ . '/../common/auth_check.php';
require_once __DIR__ . '/../common/db.php';

$stmt = $connection->prepare(
    "SELECT id, department, position, name FROM users WHERE id = ?"
);
$stmt->execute([$_SESSION['user_id']]);
$loginUser = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $connection->prepare(
    "SELECT id, department, position, name FROM users
    ORDER BY 
        CASE WHEN department = :my_dept THEN 0 ELSE 1 END,
        department,
        position"
);

$stmt->execute(['my_dept' => $loginUser['department']]);
$approverCandidates = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>稟議書 申請画面</title>

    <!-- 背景色のデザインの共通部分を読み込み -->
    <link href="../common/common.css" rel="stylesheet">
    
    <!-- 稟議用のCSSの読み込み -->
    <link rel="stylesheet" href="ringi.css">
</head>

<body>
    <form action="ringi_app.php" method="POST">
        <div class="form-container">
            <div class="title">稟議書</div>
            <table>
                <tbody>
                    <tr>
                        <th class="w-15">申請日</th>
                        <td colspan="5" class="w-85" id="today-date" style="font-weight: bold;"></td>
                    </tr>

                    <tr>
                        <th class="w-15">プロジェクト名</th>
                        <td colspan="5" class="w-85">
                            <input type="text" placeholder="プロジェクト名を入力">
                        </td>
                    </tr>

                    <tr>
                        <th class="w-15">部署名</th>
                        <td class="w-23">
                            <?= htmlspecialchars($loginUser['department'] ?? 'ー', ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <th class="w-10">職位</th>
                        <td class="w-23">
                            <?= htmlspecialchars($loginUser['position'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <th class="w-10">申請者</th>
                        <td class="w-23">
                            <?=  htmlspecialchars($loginUser['name'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                    </tr>

                    <tr>
                        <th class="w-15">費用見積</th>
                        <td colspan="5" class="w-85">
                            <input type="number" placeholder="￥">円
                        </td>
                    </tr>

                    <tr>
                        <th colspan="6" class="w-100">稟議内容（主旨・理由・目的および期待する効果等）</th>
                    </tr>
                    <tr class="content-row">
                        <td colspan="6" class="w-100">
                            <textarea placeholder="ここに詳細を記入してください"></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="approver-section" id="approver-section">
                <div class="approver-box">
                    <div class="approver-block" id="approver-list">

                        <div class="approver-row">
                            <span class="approver-label">承認者1：</span>
                            <select name="approver_ids[]" class="approver-input approver-select">
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
<?php
require_once 'db.php';
session_start();

/* --- DBからデータ取得 --- */

// 部署一覧
$departments = $mysqli->query("SELECT id, 部署名 FROM 検証用.departments")->fetch_all(MYSQLI_ASSOC);

// 職位一覧
$positions = $mysqli->query("SELECT id, 職位名 FROM 検証用.positions")->fetch_all(MYSQLI_ASSOC);

// 申請者一覧（ユーザー）
$applicants = $mysqli->query("SELECT id, username FROM 検証用.users")->fetch_all(MYSQLI_ASSOC);

// 承認者一覧（role='approver' のユーザー）
$approvers = $mysqli->query("SELECT id, username FROM 検証用.users WHERE role='approver'")->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="ja">
    <head> 
        <meta charset="UTF-8"> 
        <title>稟議書 申請画面</title>
        <link rel="stylesheet" href="ringi.css">
    </head>
<body>
    <div class="form-container">
        <div class="title">稟議書</div>
    <table>
        <tbody>
            <tr>
                <th class="w-15">申請日</th>
                <td colspan="5" class="w-85" id="today-date" style="font-weight: bold;"></td>

                <input type="hidden" name="apply_date" id="apply_date"> //申請日をPOSTするための hidden
            </tr>
            <!-- プロジェクト名 -->
            <tr>
                <th class="w-15">プロジェクト名</th>
                <td colspan="5" class="w-85"><input type="text" placeholder="プロジェクト名を入力"></td> 
            </tr>
            <tr>
                <th class="w-15">部署名</th>
                <td class="w-23">
                    <select name="department" id="department"></select>
                </td>
                <th class="w-10">職位</th>
                <td class="w-23">
                    <select name="position" id="position"></select>
                </td>
                <th class="w-10">申請者</th>
                <td class="w-23">
                    <select name="applicant" id="applicant"></select>
                </td>
            </tr>
            <tr>
                <th class="w-15">費用見積</th>
                <td colspan="2" class="w-35"><input type="text" placeholder="￥"></td>
                <th class="w-15">実施希望日</th>
                <td colspan="2" class="w-35">
                    <input type="date" name="target_date" id="target_date">
                </td>
            </tr>
            <tr>
                <th class="w-15">人数</th>
                <td colspan="2" class="w-35"><input type="text" placeholder="人"></td>
                <th class="w-15">委託先</th>
                <td colspan="2" class="w-35">
                    <div class="checkbox-group">
                        <label><input type="radio" name="itaku"> あり（</label>
                        <input type="text" class="inline-input"> ）
                        <label><input type="radio" name="itaku"> なし</label>
                    </div>
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
    <div class="approver-section">
        <span>承認者の選択:</span>
        <select name="approver" id="approver" class="approver-input">
        <?php
        foreach ($approvers as $approver){
            echo '<option value="' . $approver['id'] . '">' . $approver['username'] . '</option>';
        }
        ?> //selectにforeach埋め込み   <!-- ここにDBから承認者データが入ります -->
        </select>
    </div>

     <button type="submit" class="submit-btn">保存して確認画面へ</button> // 送信ボタン

</div>
</body>
</html>

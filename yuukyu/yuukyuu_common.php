<?php
//有給休暇申請 共通設定（記入画面 yuukyuu.php と 送信処理 yuukyuu_submit.php で共通して使う）
require_once __DIR__ . '/../common/approver.php';   // APPROVER_POSITIONS（承認できる職位の設定）

// yuukyuu.leave_type に入る値（選べる休暇の種類）
const YUUKYUU_TYPE_FULL = '有給(全休)';
const YUUKYUU_TYPE_HALF = '有給(半休)';
const YUUKYUU_LEAVE_TYPES = [YUUKYUU_TYPE_FULL, YUUKYUU_TYPE_HALF, '病気', '忌引き'];

// 有給申請で承認者に指定できる職位
// common/approver.php に 'yuukyuu' の設定があればそれを使い、無ければ稟議書('ringi')と同じ職位を使う
function yuukyuuApproverPositions(): array
{
    return APPROVER_POSITIONS['yuukyuu'] ?? APPROVER_POSITIONS['ringi'];
}

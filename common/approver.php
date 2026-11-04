<?php
// -----------共通部品----承認ルートを設定したphp
// 稟議（課長職以上）
const APPROVER_POSITIONS = [
  'ringi' =>['課長', '部長', '副社長', '社長']
];

function canApprove(?string $position, string $type): bool {
  return in_array($position, APPROVER_POSITIONS[$type] ?? [], true);
}

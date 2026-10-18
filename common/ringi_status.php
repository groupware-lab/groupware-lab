<?php 
//稟議全体の承認ステータス管理用
const RINGI_STATUS_LABELS = [
  'pending'  => ['label' => '承認待ち', 'class' => 'bg-warning text-dark'],
  'approved' => ['label' => '承認済み', 'class' => 'bg-success'],
  'returned' => ['label' => '差し戻し', 'class' => 'bg-secondary'],
  'rejected' => ['label' => '却下', 'class' => 'bg-danger'],
];

function ringiStatusLabel(string $status): array {
  return RINGI_STATUS_LABELS[$status]
    ?? ['label' => $status, 'class' => 'bg-light text-dark'];
}
?>
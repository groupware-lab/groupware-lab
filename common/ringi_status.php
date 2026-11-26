<?php 
//稟議の各承認ステータスの色の設定用
const RINGI_STATUS_LABELS = [
  'pending'  => ['label' => '承認待ち', 'class' => 'bg-warning text-dark', 'row_class' => 'list-group-item-warning'],
  'approved' => ['label' => '承認済み', 'class' => 'bg-success', 'row_class' => 'list-group-item-success'],
  'returned' => ['label' => '差し戻し', 'class' => 'bg-secondary', 'row_class' => 'list-group-item-secondary'],
  'rejected' => ['label' => '却下',     'class' => 'bg-danger', 'row_class' => 'list-group-item-danger']
];

//稟議の区分を管理
const RINGI_CATEGORY = [
  'software'      => 'ソフトウェア導入',
  'outsourcing'   => '外注費',
  'equipment'     => '備品購入',
  'business_trip' => '出張',
  'training'      => '研修参加',
  'general'       => 'その他'
];

//各承認者の承認状態からどのラベルを表示するか判定して返す
//関数名（型、引数1, 型,引数2, 型,引数3）　返却値
function approvalRouteLabel(array $a, int $currentStep, string $status): array {
  if(in_array($a['status'], ['approved', 'returned', 'rejected'], true)) {
    $s = ringiStatusLabel($a['status']);
  } elseif((int)$a['step_order'] === $currentStep && $status === 'pending') {
    $s = ringiStatusLabel('pending');
  } else {
    $s = ['label' => '未到達', 'class' => 'bg-light text-dark border', 'row_class' => ''];
  }

  $a['label'] = $s['label'];
  $a['badge'] = $s['class'];
  $a['row_class'] = $s['row_class'];

  return $a;
}

//
function ringiStatusLabel(string $status): array {
  return RINGI_STATUS_LABELS[$status]
    ?? ['label' => $status, 'class' => 'bg-light text-dark', 'row_class' =>''];
}
?>
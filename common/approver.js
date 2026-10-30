// 承認者の行を「＋」「－」で増減させる共通部品
const approverList = document.getElementById('approver-list');

// 承認者の行を上から「承認者1、承認者2…」の順にラベルを振り直す
function renumberApprovers() {
  document.querySelectorAll('.approver-row').forEach((row, index)=> {
    row.querySelector('.approver-label').textContent = '承認者' + (index + 1) + '：';
  });
}

approverList.addEventListener('click', function(e) {
  if(e.target.classList.contains('add-approver-btn')) {
   const firstRow = document.querySelector('.approver-row');
   const newRow = firstRow.cloneNode(true);
  
   //クローン元の選択状態を引き継がないようリセット
   newRow.querySelector('select').selectedIndex = 0;

   const btn = newRow.querySelector('button');
   btn.textContent = 'ー';
   btn.classList.remove('add-approver-btn');
   btn.classList.add('remove-approver-btn');
  
   approverList.appendChild(newRow);
   renumberApprovers();
   return;
  }

  if(e.target.classList.contains('remove-approver-btn')) {
    e.target.closest('.approver-row').remove();
    renumberApprovers();
  }

});
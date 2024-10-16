// complete.phpでのエラーメッセージ
function errorMsg(errorMsgTel) {
  console.log(errorMsgTel);
  const completeMsg = document.getElementById('completeMsg');

  if (errorMsgTel != '') {
    completeMsg.innerHTML = '';
    
    let errorDiv = document.createElement('div');
    let errorTel = document.createElement('p');
    errorTel.textContent = errorMsgTel;
    errorDiv.appendChild(errorTel);

    let backBtn = document.createElement('button');
    backBtn.className = 'btn btn-primary';
    backBtn.id = 'backBtn';
    backBtn.textContent = '戻る';
    backBtn.onclick = function() {
      window.history.go(-2);
    };

    completeMsg.appendChild(errorDiv);
    completeMsg.appendChild(backBtn);
  }
}

// update.phpでの完了のメッセージ
function compMsg(errorMsgTel) {
  if (errorMsgTel == '') {
    $('#liveToastComp').toast('show');
    confirmMsg.innerHTML = '';
  } else {
    confirmMsg.innerHTML = '';
    confirmMsg.textContent = errorMsgTel;
  }
}

function toastBox () {
  $('#liveToast').toast('show');
}

// リロードされた時は'ログインしました'のtoastを非表示にする
function toastLogIn () {
  // ページの到達情報を取得（ロード、リロード、進むなど）
  const navigationEntries = performance.getEntriesByType("navigation");
  // navigationEntries に何かしらの動作情報（ロード、リロード、進むなど）があった場合
  if (navigationEntries.length > 0) {
    // ページの到達方法を取得
    const navigationType = navigationEntries[0].type;
    // リンクやURLを入力して到達した場合、トーストを表示
  if (navigationType === "navigate") {
    $('#liveToastLog').toast('show');
  }
  // リロードでページに到達した場合、トーストを非表示
  if (navigationType === "reload") {
    $('#liveToastLog').toast('hide');
  }
  }
}
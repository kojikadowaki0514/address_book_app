<?php
session_start();

// ログインされていない場合は、index.phpにリダイレクト
if (!isset($_SESSION['id'])) {
  // URLに直接アクセスされた時に、toastを表示しないためのフラグ
  $_SESSION['logout'] = false;
  header('Location: index.php');
  exit();
}

// head 読み込み 
// ページタイトル
$title = '住所登録確認画面';
include 'includes/head.php';

// header 読み込み
include 'includes/header.php';
?>

<main>
  <div id="registFormContainer" class="regist-form-container">
    <form id="registForm" class="regist-form" method="post" action="complete.php">
      <h3>住所登録　確認画面</h3>
      <P class="confirm-p">登録内容をご確認いただき、問題がなければ登録ボタンを押してください。</P>
      
      <label for="post">郵便番号</label>
      <input type="text" id="con-post" name="post_code" value="<?= $_POST['post_code']; ?>" readonly>
      <label for="pref">都道府県</label>
      <input type="text" id="con-pref" name="pref" value="<?= $_POST['pref']; ?>" readonly>

      <label for="city">市区町村</label>
      <input type="text" id="con-city" name="city" value="<?= $_POST['city']; ?>" readonly>

      <label for="block">番地</label>
      <input type="text" id="con-block" name="block" value="<?= $_POST['block']; ?>" readonly>

      <label for="building">建物名</label>
      <input type="text" id="con-building" name="building" value="<?= $_POST['building']; ?>" readonly>

      <label for="tel">電話番号</label>
      <input type="tel" id="con-tel" name="tel" value="<?= $_POST['tel']; ?>" readonly>

      <label for="name">名前</label>
      <input type="text" id="con-name" name="name" value="<?= $_POST['name']; ?>" readonly>

      <label for="email">e-mail</label>
      <input type="email" id="con-email" name="email" value="<?= $_POST['email']; ?>" readonly>
      <div class="confir-btn-container">
        <button type="button" id="backBtn" onclick="history.back()" class="btn btn-primary">戻る</button>  
        <button type="submit" name="submit" id="confirmBtn" class="btn btn-primary">登録</button>
      </div>
    </form>
  </div>
</main>

<!-- footer 読み込み -->
<?php include 'includes/footer.php'; ?>
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
$title = '住所録入力フォーム';
include 'includes/head.php';

// header 読み込み
include 'includes/header.php';
?>

<div id="registFormContainer" class="regist-form-container">
  <form class="regist-form" method="post" action="confirmation.php">
    <h3>住所入力フォーム</h3>
    <label for="post">郵便番号</label>
    <input type="text" id="post" name="post_code" placeholder="123-4567" required>

    <label for="pref">都道府県</label>
    <input type="text" id="pref" name="pref" placeholder="東京都" required>

    <label for="city">市区町村</label>
    <input type="text" id="city" name="city" placeholder="◯◯区◯◯町" required>

    <label for="block">番地</label>
    <input type="text" id="block" name="block" placeholder="1-2-3">

    <label for="building">建物名</label>
    <input type="text" id="building" name="building" placeholder="◯◯◯マンション101">

    <label for="tel">電話番号</label>
    <input type="tel" id="tel" name="tel" placeholder="03-1234-5678" required>

    <label for="name">名前</label>
    <input type="text" id="name" name="name" placeholder="山田　太郎" required>

    <label for="email">e-mail</label>
    <input type="email" id="email" name="email" placeholder="yamada@example.com" required>
    <div>
      <button type="submit" id="registBtn" class="btn btn-primary w-25">確認画面</button>
    </div>
  </form>
</div>

<!-- footer 読み込み -->
<?php include 'includes/footer.php'; ?>

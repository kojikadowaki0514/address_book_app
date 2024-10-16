<?php
session_start();

if (isset($_POST['logout'])) {
  // トーストを表示するフラグをセット
  $_SESSION = array();
  // セッションを切断するにはセッションクッキーも削除する。
  // Note: セッション情報だけでなくセッションを破壊する。
  if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
              $params["path"], $params["domain"],
              $params["secure"], $params["httponly"]
    );
  }
 
  // 最終的に、セッションを破壊する
  session_destroy();

  // toastを表示するためのフラグ
  $_SESSION['show_toast'] = true;
}
?>
<body>
  <header>
    <nav class="nav">
      <div class="title-block">
        <h5 class="title"><a href="" class="title-link">住所録アプリ</a></h5>
      </div>
      <div class="register-block">
        <?php
        // index.phpまたは、account.phpの場合は'住所新規登録'は表示しない
        if (basename($_SERVER['PHP_SELF']) == 'select.php' || basename($_SERVER['PHP_SELF']) == 'select_general.php') {
          if (basename($_SERVER['PHP_SELF']) != 'select.php') {
            echo "
            <div>
              <a href='select.php' class='register-link' style='margin-right: 50px;'>一覧表へ戻る</a>
            </div>
            ";
          }
          // 一般のみ'他の人の連絡先を閲覧する'のリンクを表示する
          if ($_SESSION['authority'] != 1) {
            echo "
            <form action='select_general.php' method='post''>
              <button type='submit' name='view' class='view-link' style='margin-right: 50px;'>他の人の連絡先を閲覧する</button>
            </form> 
            ";
          }
          ?>
        <!-- <div>
          <a href="register.php" class="register-link">住所新規登録</a>
        </div> -->
        <?php } ?>
        <?php if (isset($_SESSION['id']) && basename($_SERVER['PHP_SELF']) == 'select.php' || basename($_SERVER['PHP_SELF']) == 'select_general.php') { ?>
        <form action="index.php" method="post">
          <button type="submit" name="logout" class="log-link">ログアウト</button>
        </form>
        <?php } ?>
      </div>
    </nav>
  </header>
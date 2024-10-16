<?php
// セッション開始
session_start();

session_unset();

error_reporting(E_ERROR);
ini_set('display_errors', 1);

// DB接続
include 'includes/database.php';

$email = '';
$password = '';
$error_msg_email = '';
$error_msg_password = '';

// 管理者用メールアドレス・パスワード
$authority_email = '73ciag@tjbjr.org';
$authority_password = 'jJ0@pOuB';
// 一般用メールアドレス・パスワード
$general_email = '41vyw9chkmb2c@ccymg.ac.jp';
$general_password = 'l12^7*4.';

if (isset($_POST['submit'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  
  // 初回用のパスワードでログインされたら
  if ($email == $authority_email && $password == $authority_password ||
      $email == $general_email && $password == $general_password) {
      session_regenerate_id(true);
      $_SESSION['email'] = $email;
      $_SESSION['ini_login_flg'] = true; // select.phpでtoastを表示させるためのフラグ
      header('Location: account.php');
      exit();
    } else {
        // emailをキーにログインの照合を行う
        // 動的な値を?に置き換える
        $query = 'select * from authorities where email = ?';
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        // 入力されたemailがauthoritiesテーブルに存在しなければ・・・
        if (mysqli_num_rows($result) == 0) {
          $error_msg = 'メールアドレスかパスワードが一致しません';
        }

        // 入力されたパスワードがauthoritiesテーブルに存在しなければ・・・
        while ($row = mysqli_fetch_assoc($result)) {
          if (!password_verify($password, $row['password'])) {
            $error_msg = 'メールアドレスかパスワードが一致しません';
          }
          
          if ($error_msg == '') {
            // セッションハイジャック防止のため、セッションIDを再生成
            // （古いIDを破棄し、新しいIDを作成する）
            session_regenerate_id(true);
            $_SESSION['id'] = $row['id'];
            $_SESSION['authority'] = $row['authority'];
            $_SESSION['login_flg'] = true; // select.phpでtoastを表示させるためのフラグ
            $session_id = session_id();
            setcookie('PHPSESSID', $session_id, time() + 3600, '/');
            header('Location: select.php');
            exit();
          }
        }
      }
}


// head 読み込み 
// ページタイトル
$title = 'ログイン登録ページ';
include 'includes/head.php';

// header 読み込み
include 'includes/header.php';
?>

<main>
  <div id="account" class="account-form-container">
    <form id="accountForm" class="account-form" method="post">
    <?php echo '<p class="error-msg" style="color: red;">' . $error_msg . '</p>'; ?>
      <h3>ログイン画面</h3>
        <label for="mail">メールアドレス<br><span>※半額英数字で入力してください</span></label>
        <input type="email" id="email" name="email" value="<?= $_POST['email']; ?>" required>

        <label for="password">パスワード<br><span>※半額英数字で8文字以内で入力してください</span></label>
        <input type="password" id="password" name="password" maxlength="8" value="<?= $_POST['password']; ?>" required>

        <button type="submit" name="submit" id="registBtn" class="btn btn-primary">ログイン</button>

        <P style="color: red; text-align: center; margin-top: 20px;">
          初回ログイン用アドレスとパスワードを入力した方は、<br>ログイン後、アカウント登録のページに移行します。</P>
    </form>
  </div>

  <!-- toast ログアウト成功メッセージ -->
  <div class="position-fixed top-0 p-3" style="z-index: 5; top: 0; left: 0;">
    <div id="liveToastLog" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
      <div class="toast-header">
        <!-- <img src="#" class="rounded mr-2" alt="#"> -->
        <strong class="mr-auto">ログアウトメッセージ</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="toast-body toast-btn" style="display: grid; place-items: center; margin: 0 auto;">
        <div>
          <p class="toast-msg">ログアウトしました。<p>
        </div>
      </div>
    </div>
  </div>
<main>

<?php
// データベースの切断
mysqli_close($link);

// footer 読み込み
include 'includes/footer.php';

// ログアウトをした時、toastを表示
if (isset($_SESSION['show_toast'])) {
?>
  <script>toastLogIn()</script>
<?php
  unset($_SESSION['show_toast']);
// URLに直接アクセスされた時、toastを表示しない
} elseif (!$_SESSION['logout']) {
    unset($_SESSION['logout']);
}
?>
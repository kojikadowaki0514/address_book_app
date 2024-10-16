<?php
session_start();

error_reporting(E_ERROR);
ini_set('display_errors', 1);

// DB接続
include 'includes/database.php';

mysqli_set_charset($link, 'utf8mb4');

$email = '';
$password = '';
$error_msg_email = '';
$error_msg_password = '';

if (isset($_POST['submit'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  // emailの重複チェック
  $query_email = 'select * from authorities where email = ?'; // 動的に変わる値を?で置き換える
  // SQL文の用意
  $stmt_email = mysqli_prepare($link, $query_email);
  // ?に置き換えた値を実際の値に置き換える
  mysqli_stmt_bind_param($stmt_email, 's', $email);
  // SQL文の実行
  mysqli_stmt_execute($stmt_email);
  // 実行結果を取得
  $result_email = mysqli_stmt_get_result($stmt_email);
  
  if (mysqli_num_rows($result_email) > 0) {
    $error_msg_email = '※入力されたメールアドレスはすでに登録されています。';
  }
  
  // パスワードの重複
  // authoritiesテーブルに登録されているパスワードを取得
  $query_password = 'select password from authorities';
  $stmt_password = mysqli_prepare($link, $query_password);
  mysqli_stmt_execute($stmt_password);
  $result_password = mysqli_stmt_get_result($stmt_password);

  // パスワードの重複を確認
  while ($row = mysqli_fetch_assoc($result_password)) {
      // password_verifyを使用して入力されたパスワードと既存のハッシュを比較
      if (password_verify($password, $row['password'])) {
          $error_msg_password = '※入力されたパスワードはすでに登録されています。';
      }
  }
  
  if (!$error_msg_email && !$error_msg_password) {
    // パスワードをhash化する
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    if ($_SESSION['email'] == '73ciag@tjbjr.org') {
      $query_insert = 'insert into authorities (email, password, authority) values (?, ?, 1)';
    } else {
      $query_insert = 'insert into authorities (email, password, authority) values (?, ?, 0)';
    }
    // SQL文の事前準備　動的に変わる値を?で置き換える
    $stmt = mysqli_prepare($link, $query_insert);

    // 実際の値をバインドする 'ss'はカラムの型　s > string
    mysqli_stmt_bind_param($stmt, 'ss', $email, $password_hash);

    // SQL文の実行
    mysqli_stmt_execute($stmt);

    // 挿入されたレコードのIDを取得
    $inserted_id = mysqli_insert_id($link);

    // 挿入したデータを取得するためにSELECTクエリを作成
    $query_select = 'select * from authorities where id = ?';

    $stmt_select = mysqli_prepare($link, $query_select);

    mysqli_stmt_bind_param($stmt_select, 'i', $inserted_id);

    mysqli_stmt_execute($stmt_select);

    $result = mysqli_stmt_get_result($stmt_select);

    if ($row = mysqli_fetch_assoc($result)) {
      $_SESSION['id'] = $row['id'];
      $_SESSION['email'] = $row['email'];
      $_SESSION['password'] = $row['password'];
      $_SESSION['authority'] = $row['authority'];
    }

    header('Location: register.php');
  }
}

// head 読み込み 
// ページタイトル
$title = 'アカウント登録ページ';
include 'includes/head.php';

// header 読み込み
include 'includes/header.php';
?>

<main>
  <div id="account" class="account-form-container">
    <form id="accountForm" class="account-form" method="post">
      <?php
        if ($error_msg_email) {
          echo '<p class="error-msg" style="color: red;">' . $error_msg_email . '</p>';
          }
        if ($error_msg_password) {
            echo '<p class="error-msg" style="color: red;">' . $error_msg_password . '</p>';
        }
      ?>
      <h3>アカウント登録画面</h3>
      <label for="mail">メールアドレス<br><span>※半額英数字で入力してください</span></label>
      <input type="email" id="email" name="email" value="<?= $_POST['email']; ?>" required>

      <label for="password">パスワード<br><span>※半額英数字で4文字から8文字以内で入力してください</span></label>
      <input type="password" id="password" name="password" maxlength="8" minlength="4" value="<?= $_POST['password']; ?>" required>
      
      <button type="submit" name="submit" id="registBtn" class="btn btn-primary">登録</button>
      <P style="text-align: center; color: red; margin-top: 20px;">アカウント登録後、住所の登録をしてください。</P>
    </form>
  </div>
<main>
  
<?php
// データベースの切断
mysqli_close($link);

// footer 読み込み
include 'includes/footer.php';
?>
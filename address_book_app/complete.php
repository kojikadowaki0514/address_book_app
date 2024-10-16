<?php
session_start();

// ログインされていない場合は、index.phpにリダイレクト
if (!isset($_SESSION['id'])) {
  // URLに直接アクセスされた時に、toastを表示しないためのフラグ
  $_SESSION['logout'] = false;
  header('Location: index.php');
  exit();
}

// DB接続
include 'includes/database.php';

// 文字コードの設定
// 登録した際に文字コードのエラーが発生したため、以下を設定
//  ・データベースの文字セットをutf8mb4に変更
//  ・テーブルの文字セットをutf8mb4に変更
//  ・PHP側でutf8mb4を設定
mysqli_set_charset($link, 'utf8mb4');

  $post_code = '';
  $pref = '';
  $city = '';
  $block = '';
  $building = '';
  $tel = '';
  $name = '';
  $email = '';

if (isset($_POST['submit'])) {
  $post_code = $_POST['post_code'];
  $pref = $_POST['pref'];
  $city = $_POST['city'];
  $block = $_POST['block'];
  $building = $_POST['building'];
  $tel = $_POST['tel'];
  $name = $_POST['name'];
  $email = $_POST['email'];

  // 電話番号とemailの重複チェック
  $query = 'select * from addresses where tel = ? or email = ?'; // 動的に変わる値を?で置き換える
  // SQL文の用意
  $stmt = mysqli_prepare($link, $query);
  // ?に置き換えた値を実際の値に置き換える
  mysqli_stmt_bind_param($stmt, 'ss', $tel, $email);
  // SQL文の実行
  mysqli_stmt_execute($stmt);
  // 実行結果を取得
  $result = mysqli_stmt_get_result($stmt);
  
  if (mysqli_num_rows($result) > 0) {
    $error_msg_tel = 'エラー：この電話番号またはメールアドレスはすでに登録されています。';

  } else {
    $query_insert = 'insert into addresses
                    (post_code, pref, city, block, building, tel, name, email)
                    values (?, ?, ?, ?, ?, ?, ?, ?)
                    ';

    // SQL文の事前準備　動的に変わる値を?で置き換える
    $stmt = mysqli_prepare($link, $query_insert);

    // 実際の値をバインドする 'ssssssss'はカラムの型　s > string
    mysqli_stmt_bind_param($stmt, 'ssssssss',
                $post_code, $pref, $city, $block,
                $building, $tel, $name, $email);

    // SQL文の実行
    mysqli_stmt_execute($stmt);
  }
}
// head 読み込み 
// ページタイトル
$title = '住所登録確認画面';
include 'includes/head.php';

// header 読み込み
include 'includes/header.php';
?>

<main id="completeMsg" class="complete-msg">
  <p>登録が完了しました</p>
  <button type="button" onclick="location.href='select.php'" class="btn btn-primary comp-btn">一覧ページへ</button>
</main>

<?php
// データベースの切断
mysqli_close($link);
?>

<!-- footer 読み込み -->
<?php include 'includes/footer.php'; ?>
<script>errorMsg("<?php echo $error_msg_tel ?>")</script>

<?php
session_start();

// error_reporting(E_ERROR);
// ini_set('display_errors', 1);

// DB接続
include 'includes/database.php';

// 送られてきたidの一覧表示
// idは、クエリパラメータのため、GETで取得する
if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $query = 'select * from addresses where id = ?';

  // SQL文の用意
  $stmt = mysqli_prepare($link, $query);
  // ?に置き換えた値を実際の値に置き換える
  mysqli_stmt_bind_param($stmt, 'i', $id);
  // SQL文の実行
  mysqli_stmt_execute($stmt);
  // 実行結果を取得
  $result = mysqli_stmt_get_result($stmt);
}

// update文の実行
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
  // 自分自身のレコードを含めてしまうと、違う電話番号、Emailでも重複してしまうため、自分自身のidは除く
  $query= 'select * from addresses where (tel = ? or email = ?) and id != ?'; // 動的に変わる値を?で置き換える
  
  // SQL文の用意
  $stmt = mysqli_prepare($link, $query);
  // ?に置き換えた値を実際の値に置き換える
  mysqli_stmt_bind_param($stmt, 'ssi', $tel, $email, $id);
  // SQL文の実行
  mysqli_stmt_execute($stmt);
  // 実行結果を取得
  $result_error = mysqli_stmt_get_result($stmt);

  if (mysqli_num_rows($result_error) > 0) {
    $error_msg_tel = 'エラー：この電話番号またはメールアドレスはすでに登録されています。';
  } else {
    $query_insert = 'update addresses set
                    post_code = ?,
                    pref = ?,
                    city = ?,
                    block = ?,
                    building = ?,
                    tel = ?,
                    name = ?,
                    email = ?
                    where id = ?
                    ';

    // SQL文の事前準備　動的に変わる値を?で置き換える
    $stmt = mysqli_prepare($link, $query_insert);

    // 実際の値をバインドする 'ssssssss'はカラムの型　s は string
    mysqli_stmt_bind_param($stmt, 'ssssssssi',
                           $post_code, $pref, $city, $block,
                           $building, $tel, $name, $email, $id);

    // SQL文の実行
    mysqli_stmt_execute($stmt);
    $_SESSION['error_msg_tel'] = $error_msg_tel;
    $_SESSION['complete'] = true; // select.phpでtoastを表示させるためのフラグ
    header('Location: select.php');
  }
}
?>

<?php
// head 読み込み 
// ページタイトル
$title = '住所録更新ページ';
include 'includes/head.php';

// header 読み込み
include 'includes/header.php';
?>

<main>
  <div id="registFormContainer" class="regist-form-container">
    <form action="" id="registForm" class="regist-form" method="post">
      <h3>住所登録　編集画面</h3>
      <P class="confirm-p" id="confirmMsg">変更したい内容を入力して、登録ボタンを押してください。</P>
      <?php
      if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
          ?>
          <label for="post">郵便番号</label>
          <input type="text" id="post" name="post_code" value="<?= isset($_POST['post_code']) ? $_POST['post_code'] : $row['post_code']; ?>">

          <label for="pref">都道府県</label>
          <input type="text" id="pref" name="pref" value="<?= isset($_POST['pref']) ? $_POST['pref'] : $row['pref']; ?>">

          <label for="city">市区町村</label>
          <input type="text" id="city" name="city" value="<?= isset($_POST['city']) ? $_POST['city'] : $row['city']; ?>">

          <label for="block">番地</label>
          <input type="text" id="block" name="block" value="<?= isset($_POST['block']) ? $_POST['block'] : $row['block']; ?>">

          <label for="building">建物名</label>
          <input type="text" id="building" name="building" p value="<?= isset($_POST['building']) ? $_POST['building'] : $row['building']; ?>">

          <label for="tel">電話番号</label>
          <input type="tel" id="tel" name="tel" value="<?= isset($_POST['tel']) ? $_POST['tel'] : $row['tel']; ?>">

          <label for="name">名前</label>
          <input type="text" id="name" name="name" value="<?= isset($_POST['name']) ? $_POST['name'] : $row['name']; ?>">

          <label for="email">e-mail</label>
          <input type="email" id="email" name="email" value="<?= isset($_POST['email']) ? $_POST['email'] : $row['email']; ?>">
          <div class="btn-box">
            <button type="submit" name="submit" id="confirmBtn" class="btn btn-primary w-25">登録</button>
            <button type="button" id="backBtn" onclick="location.href='select.php'" class="btn btn-primary w-25">戻る</button>          
          </div>
        <?php
          }
        }
      // データベースの切断
      mysqli_close($link);
      ?>
    </form>
  </div>
</main>
<!-- footer 読み込み -->
<?php include 'includes/footer.php';
// updateに失敗した場合のメッセージ
if (isset($_POST['submit'])) {
?>
<script>compMsg("<?php echo $error_msg_tel ?>")</script>
<?php
$error_msg_tel = '';
}
?>
<?php
// error_reporting(E_ERROR);
// ini_set('display_errors', 1);

session_start();

// ログインされていない場合は、index.phpにリダイレクト
if (!isset($_SESSION['id'])) {
  // URLに直接アクセスされた時に、toastを表示しないためのフラグ
  $_SESSION['logout'] = false;
  header('Location: index.php');
  exit();
}

// 画面表示するための関数
function tableCreateView($row, $counter) {
  return "
    <tr>
      <td>{$counter}</td>
      <td>{$row['name']}</td>
      <td>{$row['tel']}</td>
      <td>{$row['email']}</td>
    </tr>
  ";
}

// DB接続
include 'includes/database.php';

// submitが押されたら、検索処理
if (isset($_GET['search-value'])) {
  $search_value = $_GET['search-value'];
  
  $search_query = 'select name, tel, email from addresses where name like ?';

  $partial_match = "%{$search_value}%";
  
  $stmt = mysqli_prepare($link, $search_query);
  // ?に置き換えた値を実際の値に置き換える
  mysqli_stmt_bind_param($stmt, 's', $partial_match);
  // SQL文の実行
  mysqli_stmt_execute($stmt);
  // 実行結果をsearch_resultに格納
  $search_result = mysqli_stmt_get_result($stmt);
  
} else {
  // submitが押されていなければ、addressesテーブルの中身を画面表示
  $query = 'select name, tel, email from addresses';
}

// 入力値と検索結果のクリア
if (isset($_GET['reset-btn'])) {
  header('Location: select_general.php');
}

// head 読み込み 
// ページタイトル
$title = '住所一覧ページ';
include 'includes/head.php';

// header 読み込み
include 'includes/header.php';
?>

<div class="search-area">
  <form method="get">
    <input type='text' name='search-value' value='<?php echo $search_value; ?>'>
    <button type='submit' name='submit' id='search-btn' class='btn btn-primary'>検索</button>
    <button type='submit' name='reset-btn' id='reset-btn' class='btn btn-primary'>リセット</button>
  </form>
</div>
<main class="select-list">
  <table class="addresses-table">
    <tr>
      <th>No</th>
      <th>氏名</th>
      <th>電話番号</th>
      <th>メールアドレス</th>
    </tr>

    <?php
    // 画面表示項目のNoの初期値
    $counter = 1;
    
    // 検索結果がなければ・・・
    if (isset($search_result)) {
      // 検索結果のみを表示
      while ($row = mysqli_fetch_assoc($search_result)) {
        echo tableCreateView($row, $counter);
        $counter++;
      }
    } else {
        // addressesテーブルの中身を全て表示
      if ($result = mysqli_query($link, $query)) {
        foreach ($result as $row) {
          echo tableCreateView($row, $counter);
          $counter++;
        }
      }
    }
    ?>
  </table>
</main>
<?php
// データベースの切断
mysqli_close($link);
// footer 読み込み
include 'includes/footer.php';
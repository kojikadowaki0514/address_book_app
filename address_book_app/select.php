<?php
session_start();

error_reporting(E_ERROR);
ini_set('display_errors', 1);

// ログインされていない場合は、index.phpにリダイレクト
if (!isset($_SESSION['id'])) {
  // URLに直接アクセスされた時に、toastを表示しないためのフラグ
  $_SESSION['logout'] = false;
  header('Location: index.php');
  exit();
}

// 画面表示するための関数
function tableCreate($row, $counter) {
  $deleteIcon = '';

  if ($_SESSION['authority'] == 1) {
    $deleteIcon = "<td class='icon'><img src='images/delete.png' alt='削除' class='delete-icon' onclick='toastBox()'></td>";
  }

  return "
    <tr>
      <td>{$counter}</td>
      <td>{$row['post_code']}</td>
      <td>{$row['pref']}</td>
      <td>{$row['city']}</td>
      <td>{$row['block']}</td>
      <td>{$row['building']}</td>
      <td>{$row['tel']}</td>
      <td>{$row['name']}</td>
      <td>{$row['email']}</td>
      <td class='icon'><a href='update.php?id={$row['id']}'><img src='images/edit.png' alt='編集' class='edit-icon'></a></td>
      {$deleteIcon}
    </tr>
  ";
}

// DB接続
include 'includes/database.php';

// submitが押されたら、検索処理
if (isset($_GET['search-value'])) {
  $search_value = $_GET['search-value'];
  
  $search_query = 'select * from addresses
                   where name like ? or pref like ? or city like ? or block like ? or building like ?';

  $partial_match = "%{$search_value}%";
  
  $stmt = mysqli_prepare($link, $search_query);
  // ?に置き換えた値を実際の値に置き換える
  mysqli_stmt_bind_param($stmt, 'sssss', $partial_match, $partial_match, $partial_match, $partial_match, $partial_match);
  // SQL文の実行
  mysqli_stmt_execute($stmt);
  // 実行結果をsearch_resultに格納
  $search_result = mysqli_stmt_get_result($stmt);

} else {
  // submitが押されていなければ、addressesテーブルの中身を画面表示
  $query = 'select * from addresses';
}

// 入力値と検索結果のクリア
if (isset($_GET['reset-btn'])) {
  header('Location: select.php');
  exit();
}

// 管理者でなければ、自分の情報のみを表示
if ($_SESSION['authority'] != 1) {
  $query_general = 'select * from addresses where id = ?';
  $stmt_general = mysqli_prepare($link, $query_general);
  mysqli_stmt_bind_param($stmt_general, 'i', $_SESSION['id']);
  mysqli_stmt_execute($stmt_general);
  $result_general = mysqli_stmt_get_result($stmt_general);
}

// head 読み込み 
// ページタイトル
$title = '住所一覧ページ';
include 'includes/head.php';

// header 読み込み
include 'includes/header.php';
?>

<div class="search-area">
  <form method="get" action="">
  <?php
    if ($_SESSION['authority']==1) {
      echo "
      <input type='text' name='search-value' value='{$search_value}'>
      <button type='submit' name='submit' id='search-btn' class='btn btn-primary'>検索</button>
      <button type='submit' name='reset-btn' id='reset-btn' class='btn btn-primary'>リセット</button>
      ";
    }
    ?>
  </form>
</div>
<main class="select-list">
  <table class="addresses-table">
    <tr>
      <th>No</th>
      <th>郵便番号</th>
      <th>都道府県</th>
      <th>市区町村名</th>
      <th>番地</th>
      <th>建物名</th>
      <th>電話番号</th>
      <th>氏名</th>
      <th>メールアドレス</th>
      <th class='center'>編集</th>
      <?php
      if ($_SESSION['authority'] == 1 ) {
        echo "<th class='center'>削除</th>";
      }
      ?>
    </tr>
    <?php
    
    // 画面表示項目のNoの初期値
    $counter = 1;
    // 検索結果がなければ・・・
    if (!isset($search_result)) {
      // 管理者であれば、addressesテーブルを全て表示
      if ($_SESSION['authority'] == 1) {
        if ($result = mysqli_query($link, $query)) {
          foreach ($result as $row) {
            echo tableCreate($row, $counter);
            $counter++;
          }
        }
        // 一般であれば、addressesテーブルから自分のみ表示
      } else {
          while ($row = mysqli_fetch_assoc($result_general)) {
            echo tableCreate($row, $counter);
            $counter++;
          }
      }
    } else {
        // 検索結果のみを表示
        while ($row = mysqli_fetch_assoc($search_result)) {
          echo tableCreate($row, $counter);
          $counter++;
        }
    }
    ?>
  </table>
</main>
  <!-- toast 削除確認メッセージ -->
  <div class="position-fixed top-0 right-0 p-3" style="z-index: 5; right: 0; bottom: 0;">
    <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000">
      <div class="toast-header">
        <!-- <img src="#" class="rounded mr-2" alt="#"> -->
        <strong class="mr-auto">削除確認メッセージ</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="toast-body toast-btn">
        <div>
          <p class="toast-msg">本当に削除して宜しいですか？<p>
        </div>
        <div class="btn-container">
          <button type="button" id="yes-btn" class="btn btn-outline-primary" onclick="location.href='delete.php?id=<?php echo $row['id']; ?>'">はい</button>
          <button type="button" id="no-btn" class="btn btn-outline-primary" onclick="location.href='select.php'">キャンセル</button>
        </div>
      </div>
    </div>
  </div>

  <!-- toast update完了メッセージ -->
  <div class="position-fixed top-0 right-0 p-3" style="z-index: 5; top: 0; left: 35%;">
    <div id="liveToastComp" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
      <div class="toast-header">
        <strong class="mr-auto">修正完了メッセージ</strong>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="toast-up-masg toast-body">
        <p style="margin: 0 auto;">修正が完了しました。</p>
      </div>
    </div>
  </div>

  <!-- toast ログイン成功メッセージ -->
  <div class="position-fixed top-0 p-3" style="z-index: 5; top: 0; left: 35%;">
    <div id="liveToastLog" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
      <div class="toast-header">
        <!-- <img src="#" class="rounded mr-2" alt="#"> -->
        <?php
        if (isset($_SESSION['ini_login_flg'])) {
            echo '<strong class="mr-auto">アカウント登録成功メッセージ</strong>';
        } else {
            echo '<strong class="mr-auto">ログイン成功メッセージ</strong>';
        }
        ?>
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="toast-body toast-btn" style="display: grid; place-items: center; margin: 0 auto;">
        <div>
          <?php
          if (isset($_SESSION['ini_login_flg'])) {
              echo '<p class="toast-msg">アカウント登録が完了しました。</p>';
          } else {
              echo '<p class="toast-msg">ログインしました。</p>';
          }
          ?>
        </div>
      </div>
    </div>
  </div>
<?php
// データベースの切断
mysqli_close($link);
// footer 読み込み
include 'includes/footer.php';

if ($_SESSION['complete']) {
?>
  <script>compMsg("<?php echo $_SESSION['error_msg_tel'] ?>")</script>
<?php
  unset($_SESSION['error_msg_tel']);
  unset($_SESSION['complete']);
} 


if (isset($_SESSION['login_flg']) || isset($_SESSION['ini_login_flg'])) {
?>
<script>toastLogIn()</script>;
<?php
  unset($_SESSION['login_flg']); // sessionが保持され続けるため、破棄する
  unset($_SESSION['ini_login_flg']); // sessionが保持され続けるため、破棄する
}
?>
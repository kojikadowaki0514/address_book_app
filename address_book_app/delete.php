<?php
session_start();
// error_reporting(E_ERROR);
// ini_set('display_errors', 1);

// ログインされていない場合は、index.phpにリダイレクト
if (!isset($_SESSION['id'])) {
  // URLに直接アクセスされた時に、toastを表示しないためのフラグ
  $_SESSION['logout'] = false;
  header('Location: index.php');
  exit();
}

// DB接続
include 'includes/database.php';

// 送られてくるidは、クエリパラメータのため、GETで取得する
if (isset($_GET['id'])) {
  $id = $_GET['id'];

  $query = 'delete from addresses where id = ?';

  // SQL文の用意
  $stmt = mysqli_prepare($link, $query);
  // ?に置き換えた値を実際の値に置き換える
  mysqli_stmt_bind_param($stmt, 'i', $id);
  // SQL文の実行
  mysqli_stmt_execute($stmt);

  $query_id_delete = 'delete from authorities where id = ?';
  $stmt_delete = mysqli_prepare($link, $query_id_delete);
  mysqli_stmt_bind_param($stmt_delete, 'i', $id);
  mysqli_stmt_execute($stmt_delete);
  
  header('Location: select.php');
  exit();
}
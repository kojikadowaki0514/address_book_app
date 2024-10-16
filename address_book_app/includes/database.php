<?php
// データベースに接続
$link = mysqli_connect('kadowaki.naviiiva.work', 'naviiiva_user', '!Samurai1234', 'kadowaki');

// 接続確認
if (mysqli_connect_error()) {
  die("データベースに接続で来ません:" . mysqli_connect_error() . "\n");
}
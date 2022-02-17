<?php
session_start();
require '../../common/validation.php';
require '../../common/database.php';

// パラメータ取得
$user_name = $_POST['user_name'];
$user_email = $_POST['user_email'];
$user_password = $_POST['user_password'];

//バリデーション
$_SESSION['errors'] = [];

// 空チェック
emptyCheck($_SESSION['errors'], $user_name, "ユーザー名を入力して下さい");
emptyCheck($_SESSION['errors'], $user_email, "メールアドレスを入力して下さい");
emptyCheck($_SESSION['errors'], $user_password, "パスワードを入力して下さい");

// 文字数チェック
stringMaxSizeCheck($_SESSION['errors'], $user_name, "ユーザー名は255文字以内で入力して下さい", 255);
stringMaxSizeCheck($_SESSION['errors'], $user_email, "メールアドレスは255文字以内で入力して下さい", 255);
stringMaxSizeCheck($_SESSION['errors'], $user_password, "パスワードは255文字以内で入力して下さい", 255);

if(!$_SESSION['errors']){
  // メールアドレスチェック
  mailAddressCheck($_SESSION['errors'], $user_email, "正しいメールアドレスを入力して下さい");

  // メールアドレス重複チェック
  mailAddressDuplicationCheck($_SESSION['errors'], $user_email, "すでに登録されているメールアドレスです");
}

if($_SESSION['errors']){
  header('Location: ../../users');
  exit;
}
// DB接続
$database_handler = getDatabaseConnection();

try {
  // インサート文実行
  if ($statement = $database_handler->prepare('INSERT INTO users (name, email, password) VALUES (:name, :email, :password)')) {
    $password = password_hash($user_password, PASSWORD_DEFAULT);

    $statement->bindParam(':name', htmlspecialchars($user_name));
    $statement->bindParam(':email', $user_email);
    $statement->bindParam(':password', $password);
    $statement->execute();

    $_SESSION['user'] = [
      'name' => $user_name,
      'id' => $database_handler->lastInsertId()
    ];
  }
} catch (Throwable $e) {
  echo $e->getMessage();
  exit;
}

// メモ投稿画面にリダイレクト
header('Location: ../../memo/');
exit;

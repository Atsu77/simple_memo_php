<?php
/**
 * フォームが空かどうかのチェック
 * @param $errors
 * @param $check_value
 * @param $message
 */ 
function emptyCheck(&$errors, $check_value, $message){
  if(empty(trim($check_value))){ 
    array_push($errors, $message);
  }
}

/**
 * @param $errors
 * @param $check_value
 * @param $message
 * @param int $min_size
 */
function stringMinSize(&$errors, $check_value, $message, $min_size){
  if(mb_strlen($check_value) < $min_size){
    array_push($errors, $message);
  }
}

/**
 * @param $errors
 * @param $check_value
 * @param $message
 * @param $max_size
 */
function stringMaxSizeCheck(&$errors, $check_value, $message, $max_size){
  if(mb_strlen($check_value) > $max_size){
    array_push($errors, $message);
  }
}

/**
 * @param $errors
 * @param $check_value
 * @param $message
 */
function mailAddressCheck(&$errors, $check_value, $message){
  if(!filter_var($check_value, FILTER_VALIDATE_EMAIL)){
    array_push($errors, $message);
  }
}

/**
 * メールアドレスの重複チェック
 * @param $errors
 * @param $check_value
 * @param $message
 */
function mailAddressDuplicationCheck(&$errors, $check_value,$message){
  $database_handler = getDatabaseConnection();
  if($statement = $database_handler->prepare('SELECT id FROM users WHERE email = :user_email')){
    $statement->bindValue(':user_email', $check_value);
    $statement->execute();
  }

  $result = $statement->fetch(PDO::FETCH_ASSOC);
  if($result) {
    array_push($errors, $message);
  }
}
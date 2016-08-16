<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


header('X-Frame-Options: DENY');

error_reporting(0);
$init=true;
include '../../inc/db-conf.php';
include '../../inc/db_functions.php';
include '../../inc/functions.php';
include '../../inc/phpmailer/PHPMailerAutoload.php';
if (!empty($_POST['username']) && !empty($_POST['passwd']) && !empty($_POST['re_passwd'])) {
  if(db_num_rows(db_query("SELECT `id` FROM `players` WHERE `username`='".prot($_POST['username'])."' LIMIT 1"))!=0){
      echo json_encode(array('error' => 'yes', 'message' => 'Username is already taken'));
      exit();
  }
  if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
      echo json_encode(array('error' => 'yes', 'message' => 'Invalid email format'));
      exit();
  }
  if(db_num_rows(db_query("SELECT `id` FROM `players` WHERE `email`='".prot($_POST['email'])."' LIMIT 1"))!=0){
      echo json_encode(array('error' => 'yes', 'message' => 'Email is already beiing used'));
      exit();
  }
  if($_POST['passwd'] != $_POST['re_passwd']){
      echo json_encode(array('error' => 'yes', 'message' => 'Passwords don\'t match'));
      exit();
  }
    do $activation_hash = generateHash(32);
    while (db_num_rows(db_query("SELECT `activation_hash` FROM `players` WHERE `hash`='$activation_hash' LIMIT 1")) != 0);

    if (db_query("UPDATE `players` SET `username`='" . prot($_POST['username']) . "', `email`='" . prot($_POST['']) . "',`password`='" . hash('sha256', $_POST['passwd']) . "', `state`=0, `activation_hash`='$activation_hash' WHERE `hash`='" . $_COOKIE['unique_S_'] . "'") != false) {


        $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"."?verify=" . $activation_hash;

        $mail = new PHPMailer;
        $mail->setFrom('noreply@lucky.com', 'Luckzy online casino');
        $mail->addAddress($_POST['email']);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'User Registration Activation Email';
        $mail->Body    = 'Click this link to activate your account. <a href="'.$actual_link.'">"'.$actual_link .'"</a>';

        if(!$mail->send()) {
            echo json_encode(array('error' => 'yes', 'message' => 'Verification email couldn\'t be sent'));
            exit();
        } else {
            echo json_encode(array('error' => 'no'));
            exit();
        }
    } else {
        echo json_encode(array('error' => 'yes', 'message' => 'Mysql error'));
        exit();
    }
}
echo json_encode(array('error' => 'yes', 'message' => 'Fields wasn\'t filled properly'));
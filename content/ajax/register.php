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

$settings=db_fetch_array(db_query("SELECT * FROM `system` LIMIT 1"));

if (!empty($_POST['username']) && !empty($_POST['passwd']) && !empty($_POST['re_passwd']) && !empty($_POST['email'])) {
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
        while (db_num_rows(db_query("SELECT `activation_hash` FROM `players` WHERE `activation_hash`='$activation_hash' LIMIT 1")) != 0);

        $actual_link = "http://".$settings['url']."?verify=" . $activation_hash;

        if(!send_mail($_POST['email'], 'Registration activation email', '<h1>Welcome to Luckzy.com</h1><br>We\'re excited you are here! You are one step from accessing Luckzy casino. Active your account by clicking the link below!<br><br><a href="'.$actual_link.'" style="border-bottom: 2px solid #278C0B; border-radius: 3px; background: #599b47;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:33px;text-align:center;text-decoration:none;width:130px;-webkit-text-size-adjust:none;">Activate!</a><br><br>')) {
            echo json_encode(array('error' => 'yes', 'message' => 'Verification email couldn\'t be sent. Mail error: '.$mail->ErrorInfo));
            exit();
        }

    if (db_query("UPDATE `players` SET `username`='" . prot($_POST['username']) . "', `email`='" . prot($_POST['email']) . "',`password`='" . hash('sha256', prot($_POST['passwd'])) . "', `state`=0, `activation_hash`='$activation_hash' WHERE `hash`='" . $_COOKIE['unique_S_'] . "'") == false) {
        echo json_encode(array('error' => 'yes', 'message' => 'Mysql error'));
        exit();
    }
    else {
        echo json_encode(array('error' => 'no'));
        exit();
    }
}
echo json_encode(array('error' => 'yes', 'message' => 'Fields wasn\'t filled properly'));
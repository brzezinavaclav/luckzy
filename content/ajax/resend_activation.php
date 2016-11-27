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

if(!logged())exit();

$player = db_fetch_array(db_query("SELECT  `email` FROM `players` WHERE `id`=".$_SESSION['user_id']." LIMIT 1"));

$settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1"));

do $activation_hash = generateHash(32);
while (db_num_rows(db_query("SELECT `activation_hash` FROM `players` WHERE `activation_hash`='$activation_hash' LIMIT 1")) != 0);

$actual_link = "http://".$settings['url']."?verify=" . $activation_hash;

if(!send_mail($player['email'], 'Registration activation email', '<h1>Welcome to Luckzy.com</h1><br>We\'re excited you are here! You are one step from accessing Luckzy casino. Active your account by clicking the link below!<br><br><a href="'.$actual_link.'" style="border-bottom: 2px solid #278C0B; border-radius: 3px; background: #599b47;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:33px;text-align:center;text-decoration:none;width:130px;-webkit-text-size-adjust:none;">Click Here To Activate Your Account </a><br><br>')) {
    echo json_encode(array('error' => 'yes', 'message' => 'Verification email couldn\'t be sent.'));
    exit();
}

if (db_query("UPDATE `players` SET `activation_hash`='$activation_hash' WHERE `hash`='" . $_COOKIE['unique_S_'] . "'") == false) {
    echo json_encode(array('error' => 'yes', 'message' => 'Mysql error'));
    exit();
}
else {
    echo json_encode(array('error' => 'no'));
    exit();
}

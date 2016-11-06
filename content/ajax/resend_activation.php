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

$player = db_fetch_array(db_query("SELECT `activation_hash`, `email` FROM `players` WHERE `id`=".$_SESSION['user_id']." LIMIT 1"));

$settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1"));

$actual_link = "http://".$settings['url']."?verify=" . $player['activation_hash'];

if(!send_mail($player['email'], 'Registration activation email', 'Click this link to activate your account. <a href="'.$actual_link.'">'.$actual_link .'</a>')) {
    echo json_encode(array('error' => 'yes', 'message' => 'Verification email couldn\'t be sent.'));
    exit();
}

echo json_encode(array('error' => 'no'));
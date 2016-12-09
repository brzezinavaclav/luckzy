<?php
/*
 *  � CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


error_reporting(0);
header('X-Frame-Options: DENY'); 

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

if (!logged())exit();
maintenance();

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));
$settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

$amount=(double)$_GET['amount'];

if ($player['btc_balance']<$amount/$settings['btc_rate']) {
  echo json_encode(array('error'=>'yes', 'message'=>'You have insufficient funds'));
  exit();
}
if ($amount<$settings['inv_min']) {
  echo json_encode(array('error'=>'yes', 'message'=>'You cannot invest less than '.$settings['inv_min'].' Coins'));
  exit();
}

db_query("UPDATE `players` SET `balance`=`balance`-$amount, `btc_balance`=`btc_balance`-".$amount/$settings['btc_rate']." WHERE `id`=$player[id] LIMIT 1");


if (db_num_rows(db_query("SELECT `id` FROM `investors` WHERE `player_id`=$player[id] LIMIT 1"))==0) {
  db_query("INSERT INTO `investors` (`player_id`) VALUES ($player[id])");
}

$investor=db_fetch_array(db_query("SELECT * FROM `investors` WHERE `player_id`=$player[id] LIMIT 1"));


db_query("UPDATE `investors` SET `amount`=`amount`+".$amount/$settings['btc_rate']." WHERE `player_id`=$player[id] LIMIT 1");

echo json_encode(array('error'=>'no'));

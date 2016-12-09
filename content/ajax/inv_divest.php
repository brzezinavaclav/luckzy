<?php
/*
 *  � CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/

header('X-Frame-Options: DENY'); 

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';


if (!logged())exit();
maintenance();

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));
$settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

if (db_num_rows(db_query("SELECT `id` FROM `investors` WHERE `player_id`=$player[id] LIMIT 1"))==0) {
  db_query("INSERT INTO `investors` (`player_id`) VALUES ($player[id])");
}

$investor=db_fetch_array(db_query("SELECT * FROM `investors` WHERE `player_id`=$player[id] LIMIT 1"));

if ((double)$_GET['amount']<=0) {
  echo json_encode(array('error'=>'yes', 'message'=>'Amount cannot be zero'));
  exit();
}

$amount=(double)$_GET['amount'];

if ($investor['amount']*$settings['btc_rate']<$amount) {
  echo json_encode(array('error'=>'yes', 'message'=>'You cannot divest more than '.$investor['amount']*$settings['btc_rate']));
  exit();
}

db_query("UPDATE `investors` SET `amount`=`amount`-".$amount/$settings['btc_rate']." WHERE `player_id`=$player[id] LIMIT 1");


db_query("UPDATE `players` SET `balance`=`balance`+$amount, `btc_balance`=`btc_balance`+".$amount/$settings['btc_rate']." WHERE `id`=$player[id] LIMIT 1");





echo json_encode(array('error'=>'no'));

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
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/wallet_driver.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

maintenance();
if(!logged()) exit();

$player=db_fetch_array(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));



$new_addr=walletRequest('getnewaddress');
if (!db_query("INSERT INTO `deposits` (`player_id`,`address`,`currency`, `ip`) VALUES ($player[id],'$new_addr', 'btc', '".$_SERVER['REMOTE_ADDR']."')"))
  $new_addr='ERROR GENERATING ADDRESS';

echo json_encode(array('confirmed'=>$new_addr));
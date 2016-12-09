<?php
/*
 *  © CoinSlots 
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

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));

maintenance();
$balance = array('balance'=>$player['balance'], 'btc_balance' => $player['btc_balance'], 'btc_value' => bcmul($player['btc_balance'], $player['btc_rate']), 'btc_mod' => round(bcmul($player['btc_balance'], $player['btc_rate']/$player['balance']*100,2)));


$query = db_query("SELECT * FROM `currencies`");
while ($row = db_fetch_array($query)){
    $balance = array_merge($balance, array($row['currency'].'_balance' => $player[$row['currency'].'_balance'], $row['currency'].'_value' => bcmul($player[$row['currency'].'_balance'],$row['rate']), $row['currency'].'_mod' => round(bcmul($player[$row['currency'].'_balance'],$row['rate'])/$player['balance']*100,2)));
}

echo json_encode($balance);


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
include __DIR__.'/../../inc/wallet_driver.php';


$interval = 5;   // The minimal number of seconds between deposit checks.. 


$settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));


if (ini_get('safe_mode')==false) set_time_limit(0);

if (db_num_rows(db_query("SELECT * FROM `system` WHERE `id`=1 AND `deposits_last_round`<NOW()-INTERVAL $interval SECOND LIMIT 1"))==1) {

  if (!db_query("UPDATE `system` SET `deposits_last_round`=NOW() WHERE `id`=1 LIMIT 1")) exit();

  $settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

  $deposits=db_query("SELECT * FROM `deposits` WHERE `currency`='btc' AND `confirmed`=0 AND `time_generated`>NOW()-INTERVAL 1 DAY");

  $txs=walletRequest('listtransactions',array('',500));   //var_dump($txs);
  $txs=array_reverse($txs);


  while ($dp=db_fetch_array($deposits)) {

    $received=0;
    $txid='';
    foreach ($txs as $tx) {
      if ($tx['category']!='receive') continue;
      if ($tx['address']!=$dp['address']) continue;
      $received=$tx['amount'];
      break;
    }
    if ($received<$settings['btc_min_deposit']) continue;
    $txid=($tx['txid']=='')?'[unknown]':$tx['txid'];

    if ($txid == '[unknown]') continue;

    if ($dp['received']==1) {
      echo $tx['confirmations'];
      if ($tx['confirmations']>=$settings['min_confirmations'] && $dp['txid']==$txid) {
        $delExed=false;
        do {
          $delExed=db_query("UPDATE `deposits` SET `confirmed`=1 WHERE `id`=$dp[id] LIMIT 1");
        } while ($delExed==false);
        if ($delExed==true) {
          if (db_num_rows(db_query("SELECT `id` FROM `transactions` WHERE `txid`='$dp[txid]' AND `txid`!='[unknown]' LIMIT 1"))!=0) continue;
          db_query("UPDATE `players` SET `balance`=TRUNCATE(ROUND((`balance`+$received),9),8) WHERE `id`=$dp[player_id] LIMIT 1");
          db_query("INSERT INTO `transactions` (`player_id`,`amount`,`txid`) VALUES ($dp[player_id],$dp[amount],'$dp[txid]')");
        }
      }
      continue;
    }

    db_query("UPDATE `deposits` SET `received`=1,`amount`=$received,`coins_amount`=".$received*$settings['btc_rate'].",`txid`='$txid' WHERE `id`=$dp[id] LIMIT 1");
  }
  db_query("DELETE FROM `deposits` WHERE `currency`='btc' AND `time_generated`<NOW()-INTERVAL 7 DAY");
}


if ($settings['maintenance']) $mt = 'yes'; else $mt = 'no';

echo json_encode(array('maintenance' => $mt));

?>

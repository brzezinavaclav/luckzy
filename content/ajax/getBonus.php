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
include __DIR__.'/../../inc/wallet_driver.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';


if (!logged())exit();
maintenance();

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));
$settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));


if($player['state'] != 1){
  echo json_encode(array('error'=>'yes', 'message'=>'Please activate your account before making any transactions.'));
  exit();
}

if ($settings['giveaway']!=1) exit();


$captcha=$_SESSION['giveaway_captcha'];

$_SESSION['giveaway_captcha']=generateHash(7);

if (empty($captcha) || empty($_GET['sol']) || strtoupper($_GET['sol'])!=$captcha) {
  echo json_encode(array('error'=>'yes','content'=>'captcha'));
  exit();
}


db_query("DELETE FROM `giveaway_ip_limit` WHERE `ip`='".$_SERVER['REMOTE_ADDR']."' AND `claimed`<NOW()-INTERVAL $settings[giveaway_freq] SECOND");
if (db_num_rows(db_query("SELECT `id` FROM `giveaway_ip_limit` WHERE `ip`='".$_SERVER['REMOTE_ADDR']."' LIMIT 1"))!=0) {
  echo json_encode(array('error'=>'yes','content'=>'time'));
  exit();  
}
if ($player['balance']!=0) {
  echo json_encode(array('error'=>'yes','content'=>'balance'));
  exit();  
}


if($player['currency_preference']==0){
  $player = db_fetch_array(db_query("SELECT * FROM `players` WHERE `id`=".$_SESSION['user_id']));
  $settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

  $currency = db_fetch_array(db_query("SELECT `currency`,`rate` FROM `currencies`"));
  db_query("UPDATE `players` SET `".$currency['currency']."_balance`=`".$currency['currency']."_balance`+".$settings['giveaway_amount']/$currency['rate']." WHERE `id`=$player[id] LIMIT 1");
}

else if($player['currency_preference']==1){
  $player = db_fetch_array(db_query("SELECT * FROM `players` WHERE `id`=".$_SESSION['user_id']));
  $settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));


  $users_bal=db_fetch_array(db_query("SELECT SUM(`balance`) AS `sum` FROM `users`"));
  $deposits=db_fetch_array(db_query("SELECT SUM(`amount`) AS `sum` FROM `deposits`"));
  $fbalance=(walletRequest('getbalance')-$users_bal['sum']-$deposits['sum']);

  if ($settings['giveaway_amount']>$fbalance) {
    echo json_encode(array('error'=>'yes','content'=>'no_funds'));
    exit();
  }
  db_query("UPDATE `players` SET `btc_balance`=`btc_balance`+".$settings['giveaway_amount']/$settings['btc_rate']." WHERE `id`=$player[id] LIMIT 1");
}

else{
  $p = rand(0,1);

  if($p) {
    $player = db_fetch_array(db_query("SELECT * FROM `players` WHERE `id`=".$_SESSION['user_id']));
    $settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

    $currency = db_fetch_array(db_query("SELECT `currency`,`rate` FROM `currencies`"));
    db_query("UPDATE `players` SET `".$currency['currency']."_balance`=`".$currency['currency']."_balance`+".$settings['giveaway_amount']/$currency['rate']." WHERE `id`=$player[id] LIMIT 1");
  }
  else {
    $player = db_fetch_array(db_query("SELECT * FROM `players` WHERE `id`=".$_SESSION['user_id']));
    $settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));


    $users_bal=db_fetch_array(db_query("SELECT SUM(`balance`) AS `sum` FROM `users`"));
    $deposits=db_fetch_array(db_query("SELECT SUM(`amount`) AS `sum` FROM `deposits`"));
    $fbalance=(walletRequest('getbalance')-$users_bal['sum']-$deposits['sum']);

    if ($settings['giveaway_amount']>$fbalance) {
      echo json_encode(array('error'=>'yes','content'=>'no_funds'));
      exit();
    }
    db_query("UPDATE `players` SET `btc_balance`=`btc_balance`+".$settings['giveaway_amount']/$settings['btc_rate']." WHERE `id`=$player[id] LIMIT 1");
  }
}


db_query("INSERT INTO `giveaway_ip_limit` (`ip`) VALUES ('".$_SERVER['REMOTE_ADDR']."')");
db_query("UPDATE `players` SET `balance`=$settings[giveaway_amount] WHERE `id`=$player[id] LIMIT 1");

echo json_encode(array('error'=>'no'));

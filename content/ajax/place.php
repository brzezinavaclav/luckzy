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
db_query('BEGIN TRANSACTION');

if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1 FOR UPDATE"))==0) exit();


maintenance();

$settings=db_fetch_array(db_query("SELECT * FROM `system` LIMIT 1"));

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));


if (!isset($_GET['w']) || (double)$_GET['w']<0 || (double)$_GET['w']>$player['balance']) {     // bet amount
  echo json_encode(array('error' => 'Invalid bet'));
  exit();
}

$wager = (double)$_GET['w'];
if ($wager < $settings['min_bet'] && $wager != 0) {
  echo json_encode(array('error' => 'Your bet is too small'));
  exit();
}

if ($wager > $settings['bankroll_maxbet_ratio']) {
  echo json_encode(array('error' => 'Your bet is too big'));
  exit();
}

  $server_seed = $player['dice_seed'];
  $client_seed = (int)$player['client_seed'];

  $multiplier=round((double)$_GET['m'],2);
  $under_over=(int)$_GET['hl'];

  $chance['under']=floor((1/($multiplier/100)*((100-$settings['house_edge'])/100))*100)/100;
  $chance['over']=100-$chance['under'];

  $result = (double)(($server_seed + $client_seed) % 10000)/100;
  if(($under_over==0 && $result<=$chance['under']) || ($under_over==1 && $result>=$chance['over'])) {
    $win_lose = 1;
  }
  else{
    $win_lose = 0;
    $multiplier = 0;
  }

  //new seed
  $newSeed    = random_num(8);
  $newCSeed   = random_num(8);


$payout = $wager * $multiplier;
$profit = ($wager * -1) + $payout;



$player_q = db_query("SELECT * FROM `players` WHERE `id`=$player[id] AND `balance` >= $wager LIMIT 1");
if (db_num_rows($player_q) == 0) {
  echo json_encode(array('error' => 'Invalid bet'));
  exit();
}
$player = db_fetch_array($player_q);


$newBalance = $player['balance'] + $profit;



if ($settings['inv_enable'] == 1 && $profit != 0) {

  $sFreeBalance = db_fetch_array(db_query("SELECT SUM(`balance`) AS `sum` FROM `players`"));
  $sFreeBalance = $sFreeBalance['sum'];

  $cas_profit = $profit*-1;

  $inv_invest = db_fetch_array(mysql_query("SELECT SUM(`amount`) AS `sum` FROM `investors` WHERE `amount`!=0"));
  $inv_invest = $inv_invest['sum'];
  $cas_invest = ($sFreeBalance - $inv_invest);

  db_query("UPDATE `investors` SET `amount`=(`amount`+(($cas_profit/100)*((`amount`/$sFreeBalance)*(100-$settings[inv_perc])))),`profit`=(`profit`+(($cas_profit/100)*((`amount`/$sFreeBalance)*(100-$settings[inv_perc])))) WHERE `amount`!=0");

  $cas_percprofit = 0;

  $q = db_query("SELECT * FROM `investors` WHERE `amount`!=0");
  while ($inv = db_fetch_array($q)) {
    $cas_percprofit += (($cas_profit/100)*(($inv['amount']/$sFreeBalance)*($settings['inv_perc'])));
  }

  db_query("UPDATE `system` SET `inv_casprofit`=(`inv_casprofit`+(($cas_profit/100)*(($cas_invest/$sFreeBalance)*(100)))+$cas_percprofit) LIMIT 1");
}


db_query("UPDATE `players` SET `balance`=$newBalance WHERE `id`=$player[id] LIMIT 1");
db_query("INSERT INTO `spins` (`player`,`bet_amount`,`server_seed`,`client_seed`,`result`,`multiplier`,`payout`, `game`) VALUES ($player[id],$wager,'".$player['dice_seed']."','$client_seed','".$result."',$multiplier,$payout, 'dice')");


db_query("UPDATE `players` SET `last_dice_seed`=`dice_seed`,`dice_seed`='$newSeed',`last_client_seed`=`client_seed`,`client_seed`='$newCSeed',`dice_last_result`='$result' WHERE `id`=$player[id] LIMIT 1");

  echo json_encode(array(
      'result'=>$result,
      'win_lose' => $win_lose,
      'profit'=> $profit,
      'fair'  =>  array(

          'newSeed'           => hash( 'sha256', $newSeed ),
          'newCSeed'          => $newCSeed,
          'lastSeed_sha256'   => hash( 'sha256', $player['dice_seed'] ),
          'lastSeed'          => $player['dice_seed'],
          'lastCSeed'         => $client_seed,
          'lastResult'        => "$result"

      ),
  ));


db_query('COMMIT TRANSACTION');
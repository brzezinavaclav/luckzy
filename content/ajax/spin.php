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

db_query('START TRANSACTION');

if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1 FOR UPDATE"))==0) exit();


maintenance();

$settings=db_fetch_array(db_query("SELECT * FROM `system` LIMIT 1"));

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));


if (!isset($_GET['w']) || (double)$_GET['w']<0 || (double)$_GET['w']>$player['balance']) {     // bet amount
  echo json_encode(array('error' => 'Invalid bet'));
  exit();
}


if (!isset($_GET['w']) || (double)$_GET['w']<0) {     // bet amount
    echo json_encode(array('error' => 'Invalid bet'));
    exit();
}

if((double)$_GET['w']>$player['balance']){
    echo json_encode(array('error' => 'You have unsuficient funds'));
    exit();
}

if ((double)$_GET['w'] > $settings['bankroll_maxbet_ratio']) {
  echo json_encode(array('error' => 'Your bet is too big'));
  exit();  
}

$wager = (double)$_GET['w'];

$server_seed = unserialize($player['slots_seed']);
$client_seed = (int)$player['client_seed'];

$r_win = 0; $r_lose = 0; $r_tie = 0;$multiplier = 0;$newSeed = 0; $newCSeed = 0; $result = '';
$index = bcmod(bcadd($server_seed['seed_num'],$client_seed),128);

  (int)$result1 = $server_seed['wheel1'][$index];
  (int)$result2 = $server_seed['wheel2'][$index];
  (int)$result3 = $server_seed['wheel3'][$index];

  if ($result1 == 1 && $result2 == 1 && $result3 == 1)
    $multiplier = $settings['jackpot'];
  else if ($result1 == 2 && $result2 == 2 && $result3 == 2)
    $multiplier = 600;
  else if ($result1 == 3 && $result2 == 3 && $result3 == 3)
    $multiplier = 200;
  else if ($result1 == 4 && $result2 == 4 && $result3 == 4)
    $multiplier = 50;
  else if ($result1 == 5 && $result2 == 5 && $result3 == 5)
    $multiplier = 10;
  else if ($result1 == 6 && $result2 == 6 && $result3 == 6)
    $multiplier = 5;
  else if (($result1 == 6 && $result2 == 6) || ($result1 == 6 && $result3 == 6) || ($result2 == 6 && $result3 == 6))
    $multiplier = 2;
  else if ($result1 == 6 || $result2 == 6 || $result3 == 6)
    $multiplier = 1;
  else
    $multiplier = 0;

  $result = $result1.','.$result2.','.$result3;

  if ($multiplier < 1) $r_lose = 1;
  else if ($multiplier > 1) $r_win = 1;
  else $r_tie = 1;
  //new seed
  $newSeed    = serialize( generateSlotsSeed());
  $newCSeed   = random_num(32);

$payout = $wager * $multiplier;
$profit = ($wager * -1) + $payout;



$player_q = db_query("SELECT * FROM `players` WHERE `id`=$player[id] AND `balance` >= $wager LIMIT 1 FOR UPDATE");
if (db_num_rows($player_q) == 0) {
  echo json_encode(array('error' => 'Invalid bet'));
  exit();
}
$player = db_fetch_array($player_q);

if($player['currency_preference']==0){
    currencies_preference($profit, $wager, $multiplier);
}

else if($player['currency_preference']==1){
    btc_preference($profit);
}

else{
    random_preference($profit, $wager, $multiplier);
}

$newBalance = $player['balance'] + $profit;



if ($settings['inv_enable'] == 1 && $profit != 0) {
  
  $sFreeBalance = db_fetch_array(db_query("SELECT SUM(`balance`) AS `sum` FROM `players`"));
  $sFreeBalance = $sFreeBalance['sum'];
  
  $cas_profit = $profit*-1;
  
  $inv_invest = db_fetch_array(db_query("SELECT SUM(`amount`) AS `sum` FROM `investors` WHERE `amount`!=0"));
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
db_query("INSERT INTO `spins` (`player`,`bet_amount`,`server_seed`,`client_seed`,`result`,`multiplier`,`payout`, `game`) VALUES ($player[id],$wager,'".$player['slots_seed']."','$client_seed','".$result."',$multiplier,$payout, 'slots')");


db_query("UPDATE `players` SET `last_slots_seed`=`slots_seed`,`slots_seed`='$newSeed',`last_client_seed`=`client_seed`,`client_seed`='$newCSeed',`slots_last_result`='$result' WHERE `id`=$player[id] LIMIT 1");

  echo json_encode(array(
      'error' => 'no',
      'result' => $result,
      'fair' => array(

          'newSeed' => hash('sha256', slotsSeedExport($newSeed)),
          'newCSeed' => $newCSeed,
          'lastSeed_sha256' => hash('sha256', slotsSeedExport($player['slots_seed'])),
          'lastSeed' => slotsSeedExport($player['slots_seed']),
          'lastCSeed' => $client_seed,
          'lastResult' => "$result1,$result2,$result3"

      ),
      'items' => array(
          'wheel1' => $server_seed['wheel1'],
          'wheel2' => $server_seed['wheel2'],
          'wheel3' => $server_seed['wheel3']
      ),
      'index' => $index

  ));


db_query('COMMIT');
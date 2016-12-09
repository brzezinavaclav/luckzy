<?php
/*
 *  © CoinJack
 *  Demo: http://www.btcircle.com/coinjack
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


if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"))==0) exit();


$settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

if (empty($_GET['wager']) || (double)$_GET['wager']<0) $wager=0; else $wager=(double)$_GET['wager'];

if ($wager>$settings['bankroll_maxbet_ratio']) {
  echo json_encode(array('error'=>'yes','content'=>'too_big'));
  exit();
}



$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1 FOR UPDATE"));


if (db_num_rows(db_query("SELECT `id` FROM `games` WHERE `player`=$player[id] AND `ended`=0 LIMIT 1"))!=0) {
  echo json_encode(array('error'=>'yes','content'=>'playing'));
  exit();
}

if ($wager>$player['balance']) {
  echo json_encode(array('error'=>'yes','content'=>'balance'));
  exit();
}


// ... AND THE SHOW MUST GO ON

db_query("DELETE FROM `insurance_process` WHERE `player`=$player[id]");


db_query("UPDATE `players` SET `balance`=(`balance`-$wager) WHERE `id`=$player[id] LIMIT 1");


$initial_shuffle=unserialize($player['initial_shuffle']);
$client_seed=$player['client_seed'];

$final_shuffle['initial_array']=cs_shuffle($client_seed,$initial_shuffle['initial_array']);


$player_deck=array(
  $final_shuffle['initial_array'][0],
  $final_shuffle['initial_array'][2]
);
$dealer_deck=array(
  $final_shuffle['initial_array'][1],
  $final_shuffle['initial_array'][3]
);

$used_cards=4;

$cards=array(
  'dealer-1' => explode('_',$dealer_deck[0]),
  'dealer-2' => explode('_',$dealer_deck[1]),
  'player-1' => explode('_',$player_deck[0]),
  'player-2' => explode('_',$player_deck[1]),
);


if (card_value($cards['player-1'][1])==card_value($cards['player-2'][1])) $accessable=2;
else $accessable=1;

$i_process='games';

if ($cards['dealer-1'][1]=='A' && $settings['insurance']==1) $i_process='insurance_process';

db_query("INSERT INTO `$i_process` (`player`,`bet_amount`,`player_deck`,`dealer_deck`,`initial_shuffle`,`client_seed`,`final_shuffle`,`used_cards`,`accessable_actions`) VALUES ($player[id],$wager,'".serialize($player_deck)."','".serialize($dealer_deck)."','$player[initial_shuffle]','$client_seed','".serialize($final_shuffle)."',$used_cards,$accessable)");



if ($i_process=='games') {

  $id = db_fetch_array(db_query("SELECT `id` FROM `games` ORDER BY `id` DESC LIMIT 1"));
  $gameID=$id['id'];


  if($player['currency_preference']==0){
    $_wager = $wager;
    $q = db_query("SELECT `currency`,`rate` FROM `currencies`");
    while ($_wager > 0) {
      $c = db_fetch_array($q);
      if($c == false){
        db_query("UPDATE `players` SET `btc_balance`=`btc_balance`-".$_wager/$settings['btc_rate']." WHERE `id`=$player[id] LIMIT 1");
        $_wager = 0;
      }
      else{
        if($_wager - $player[$c['currency'].'_balance']*$c['rate'] > 0) {
          db_query("UPDATE `players` SET `".$c['currency']."_balance`=0 WHERE `id`=$player[id] LIMIT 1");
          db_query("UPDATE `games` SET `".$c['currency']."_bet_amount`=".$player[$c['currency'].'_balance']." WHERE `id`=$gameID LIMIT 1");
          $_wager -= $player[$c['currency'].'_balance']*$c['rate'];
        }
        else{
          db_query("UPDATE `players` SET `".$c['currency']."_balance`=`".$c['currency']."_balance`-".$_wager/$c['rate']." WHERE `id`=$player[id] LIMIT 1");
          db_query("UPDATE `games` SET `".$c['currency']."_bet_amount`=".$_wager/$c['rate']." WHERE `id`=$gameID LIMIT 1");
          $_wager = 0;
        }
      }
    }
  }

  else if($player['currency_preference']==1){
    $_wager = $wager;
    if($_wager - ($player['btc_balance']*$player['btc_rate']) > 0){
      $_wager -= $player['btc_balance']*$player['btc_rate'];
      db_query("UPDATE `players` SET `btc_balance`=0 WHERE `id`=$player[id] LIMIT 1");
      db_query("UPDATE `games` SET `btc_bet_amount`=".$player['btc_balance']." WHERE `id`=$gameID LIMIT 1");
      $q = db_query("SELECT `currency`, `rate` FROM `currencies`");
      while ($_wager > 0){
        $c = db_fetch_array($q);
        if($_wager - $player[$c['currency'].'_balance']*$c['rate'] > 0) {
          db_query("UPDATE `players` SET `".$c['currency']."_balance`=0 WHERE `id`=$player[id] LIMIT 1");
          db_query("UPDATE `games` SET `".$c['currency']."_bet_amount`=".$player[$c['currency'].'_balance']." WHERE `id`=$gameID LIMIT 1");
          $_wager -= $player[$c['currency'].'_balance']*$c['rate'];
        }
        else{
          db_query("UPDATE `players` SET `".$c['currency']."_balance`=`".$c['currency']."_balance`-".$_wager/$c['rate']." WHERE `id`=$player[id] LIMIT 1");
          db_query("UPDATE `games` SET `".$c['currency']."_bet_amount`=".$_wager/$c['rate']." WHERE `id`=$gameID LIMIT 1");
          $_wager = 0;
        }
      }
    }
    else{
      db_query("UPDATE `players` SET `btc_balance`=`btc_balance`-".$_wager/$player['btc_rate']." WHERE `id`=$player[id] LIMIT 1");
      db_query("UPDATE `games` SET `btc_bet_amount`=".$_wager/$player['btc_rate']." WHERE `id`=$gameID LIMIT 1");
      $_wager = 0;
    }

  }

  else{
    $p = rand(0,1);
    if($p){
      $_wager = $wager;
      $q = db_query("SELECT `currency`,`rate` FROM `currencies`");
      while ($_wager > 0) {
        $c = db_fetch_array($q);
        if($c == false){
          db_query("UPDATE `players` SET `btc_balance`=`btc_balance`-".$_wager/$settings['btc_rate']." WHERE `id`=$player[id] LIMIT 1");
          $_wager = 0;
        }
        else{
          if($_wager - $player[$c['currency'].'_balance']*$c['rate'] < 0) {
            db_query("UPDATE `players` SET `".$c['currency']."_balance`=0 WHERE `id`=$player[id] LIMIT 1");
            db_query("UPDATE `games` SET `".$c['currency']."_bet_amount`=".($_wager - $player[$c['currency'].'_balance']*$c['rate'])/$c['rate']." WHERE `id`=$gameID LIMIT 1");
            $_wager -= $player[$c['currency'].'_balance']*$c['rate'];
          }
          else{
            db_query("UPDATE `players` SET `".$c['currency']."_balance`=`".$c['currency']."_balance`-".$_wager/$c['rate']." WHERE `id`=$player[id] LIMIT 1");
            db_query("UPDATE `games` SET `".$c['currency']."_bet_amount`=".$_wager/$c['rate']." WHERE `id`=$gameID LIMIT 1");
            $_wager = 0;
          }
        }
      }
    }
    else{
      $_wager = $wager;
      if($_wager - ($player['btc_balance']*$player['btc_rate']) < 0){
        $_wager -= $player['btc_balance']*$player['btc_rate'];
        db_query("UPDATE `players` SET `btc_balance`=0 WHERE `id`=$player[id] LIMIT 1");
        $q = db_query("SELECT `currency`, `rate` FROM `currencies`");
        while ($_wager > 0){
          $c = db_fetch_array($q);
          if($_wager - $player[$c['currency'].'_balance']*$c['rate'] < 0) {
            db_query("UPDATE `players` SET `".$c['currency']."_balance`=0 WHERE `id`=$player[id] LIMIT 1");
            db_query("UPDATE `games` SET `".$c['currency']."_bet_amount`=".($_wager - $player[$c['currency'].'_balance']*$c['rate'])/$c['rate']." WHERE `id`=$gameID LIMIT 1");
            $_wager -= $player[$c['currency'].'_balance']*$c['rate'];
          }
          else{
            db_query("UPDATE `players` SET `".$c['currency']."_balance`=`".$c['currency']."_balance`-".$_wager/$c['rate']." WHERE `id`=$player[id] LIMIT 1");
            db_query("UPDATE `games` SET `".$c['currency']."_bet_amount`=".$_wager/$c['rate']." WHERE `id`=$gameID LIMIT 1");
            $_wager = 0;
          }
        }
      }
      else{
        db_query("UPDATE `players` SET `btc_balance`=`btc_balance`-".$_wager/$player['btc_rate']." WHERE `id`=$player[id] LIMIT 1");
        db_query("UPDATE `games` SET `btc_bet_amount`=".$_wager/$player['btc_rate']." WHERE `id`=$gameID LIMIT 1");
        $_wager = 0;
      }
    }
  }

  $dealerSums=getSums($dealer_deck);
  $playerSums=getSums($player_deck);

  $data['winner']='-';

  if (in_array(21,$dealerSums)) {
    $accessable=0;
    if (in_array(21,$playerSums)) {
      db_query("UPDATE `games` SET `ended`=1,`winner`='tie' WHERE `id`=$gameID LIMIT 1");
      $winner='tie';
      $data['winner']='tie';
      playerWon($player['id'],$gameID,$wager,$dealer_deck,'tie',true,serialize($final_shuffle));
    }
    else {
      db_query("UPDATE `games` SET `ended`=1,`winner`='dealer' WHERE `id`=$gameID LIMIT 1");
      $winner='dealer';
      $data['winner']='dealer';
      playerWon($player['id'],$gameID,$wager,$dealer_deck,'lose',true,serialize($final_shuffle));
      $fair=db_fetch_array(db_query("SELECT `client_seed`, `last_client_seed`, `initial_shuffle`, `last_initial_shuffle`, `last_final_shuffle` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));
      $data['fair'] = array(
          'newSeed'           => hash( 'sha256', stringify_shuffle($fair['initial_shuffle']) ),
          'newCSeed'          => $fair['client_seed'],
          'lastSeed_sha256'   => hash( 'sha256', stringify_shuffle($fair['last_initial_shuffle'] )),
          'lastSeed'          => stringify_shuffle($fair['last_initial_shuffle']),
          'lastCSeed'         => $fair['last_client_seed'],
          'lastResult'        => stringify_shuffle($fair['last_final_shuffle'])
      );
    }
  }
  else if (in_array(21,$playerSums)) {
    db_query("UPDATE `games` SET `ended`=1,`winner`='player' WHERE `id`=$gameID LIMIT 1");
    $accessable=0;
    $winner='player';
    $data['winner']='player';
    playerWon($player['id'],$gameID,$wager,$dealer_deck,'regular',true,serialize($final_shuffle));
    $fair=db_fetch_array(db_query("SELECT `client_seed`, `last_client_seed`, `initial_shuffle`, `last_initial_shuffle`, `last_final_shuffle` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));
    $data['fair'] = array(
        'newSeed'           => hash( 'sha256', stringify_shuffle($fair['initial_shuffle']) ),
        'newCSeed'          => $fair['client_seed'],
        'lastSeed_sha256'   => hash( 'sha256', stringify_shuffle($fair['last_initial_shuffle'] )),
        'lastSeed'          => stringify_shuffle($fair['last_initial_shuffle']),
        'lastCSeed'         => $fair['last_client_seed'],
        'lastResult'        => stringify_shuffle($fair['last_final_shuffle'])
    );
  }
  else {
    $cards['dealer-2'][0]='-';
    $cards['dealer-2'][1]='-';
    $winner='-';
    $dealerSums='-';
  }

  if ($dealerSums!='-') $dealerSums=implode(',',$dealerSums);
  $playerSums=implode(',',$playerSums);

  echo json_encode(array('error'=>'no','content'=>$cards,'insured'=>'no','sums'=>array('dealer'=>$dealerSums,'player'=>$playerSums),'wager'=>n_num($wager,true),'accessable'=>$accessable,'winner'=>$winner,'data'=>$data));


}

else {    // INSURANCE PROCESS

  //$cards['player-2']='';

  $playerSums=implode(',',getSums($player_deck));
  $dealerSums='1,11';

  $cards['dealer-2'] = '';

  $data=array('insurance'=>'yes');

  echo json_encode(array('error'=>'no','content'=>$cards,'insured'=>'-','sums'=>array('dealer'=>$dealerSums,'player'=>$playerSums),'wager'=>n_num($wager,true),'data'=>$data));

}



db_query('COMMIT');

?>

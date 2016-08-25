<?php
/*
 *  © CoinJack 
 *  Demo: http://www.btcircle.com/coinjack
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


header('X-Frame-Options: DENY'); 

$init=true;

include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

db_query('START TRANSACTION');

if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"))==0) exit();


$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1 FOR UPDATE"));

$settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

$insrow=db_fetch_array(db_query("SELECT * FROM `insurance_process` WHERE `player`=$player[id] LIMIT 1"));

if (empty($_GET['ans']) || $_GET['ans']!=1) $insured_=false; else $insured_=true;

$insured=false;
if ($insrow['bet_amount']!=0 && $player['balance']>=($insrow['bet_amount']/2) && $insured_==true) {
  if (db_query("UPDATE `players` SET `balance`=`balance`-".($insrow['bet_amount']/2)." WHERE `id`=$player[id] LIMIT 1"))
    $insured=true;
}

$tmp_table_s=generateHash(6);


db_query("UPDATE `temp_table_$tmp_table_s` SET `id`=NULL");
db_query("INSERT INTO `games` (`player`,`player_deck`,`player_deck_stand`,`player_deck_2`,`player_deck_2_stand`,`dealer_deck`,`ended`,`bet_amount`,`winner`,`multiplier`,`time`,`initial_shuffle`,`client_seed`,`final_shuffle`,`used_cards`,`accessable_actions`,`canhit`,`insurance_process`,`note`) VALUES('".$insrow['player']."','".$insrow['player_deck']."','".$insrow['player_deck_stand']."','".$insrow['player_deck_2']."','".$insrow['player_deck_2_stand']."','".$insrow['dealer_deck']."','".$insrow['ended']."','".$insrow['bet_amount']."','".$insrow['winner']."','".$insrow['multiplier']."','".$insrow['time']."','".$insrow['initial_shuffle']."','".$insrow['client_seed']."','".$insrow['final_shuffle']."','".$insrow['used_cards']."','".$insrow['accessable_actions']."','".$insrow['canhit']."','".$insrow['insurance_process']."','".$insrow['note']."')");

$gameID=db_last_insert_id();
db_query("DELETE FROM `insurance_process` WHERE `id`=$insrow[id] LIMIT 1");
  
$wager=$insrow['bet_amount'];
$dealer_deck=unserialize($insrow['dealer_deck']);
$player_deck=unserialize($insrow['player_deck']);
$final_shuffle=unserialize($insrow['final_shuffle']);

$dealerSums=getSums($dealer_deck);
$playerSums=getSums($player_deck);
  
$data['winner']='-';

$gameended=true;
    
if (in_array(21,$dealerSums)) {
  $accessable=0;
  if (in_array(21,$playerSums)) {
    db_query("UPDATE `games` SET `ended`=1,`winner`='tie' WHERE `id`=$gameID LIMIT 1");
    $winner='tie';
    $data['winner']='tie';
    playerWon($player['id'],$gameID,$wager,$dealer_deck,'tie',true,serialize($final_shuffle));
    if ($insured)
      db_query("UPDATE `players` SET `balance`=`balance`+$wager WHERE `id`=$player[id] LIMIT 1");
  }
  else {    
    db_query("UPDATE `games` SET `ended`=1,`winner`='dealer' WHERE `id`=$gameID LIMIT 1");
    $winner='dealer';
    $data['winner']='dealer';
    playerWon($player['id'],$gameID,$wager,$dealer_deck,'lose',true,serialize($final_shuffle));
    if ($insured)
      db_query("UPDATE `players` SET `balance`=`balance`+$wager WHERE `id`=$player[id] LIMIT 1");
  }
}  
else if (in_array(21,$playerSums)) {
  db_query("UPDATE `games` SET `ended`=1,`winner`='player' WHERE `id`=$gameID LIMIT 1");
  $accessable=0;
  $winner='player';
  $data['winner']='player';
  playerWon($player['id'],$gameID,$wager,$dealer_deck,'regular',true,serialize($final_shuffle));
}
else {
  $cards['dealer-2'][0]='-';
  $cards['dealer-2'][1]='-';
  $winner='-';
  $dealerSums='-';
  $gameended=false;
}
  
if ($gameended) {
  db_query("UPDATE `games` SET `insurance_process`=1 WHERE `id`=$gameID");
}

echo json_encode(array('error'=>'no'));


db_query('COMMIT');
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
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';


if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"))==0) exit();


$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));

$settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

$insrow=db_fetch_array(db_query("SELECT * FROM `insurance_process` WHERE `player`=$player[id] LIMIT 1"));

if (empty($_GET['ans']) || $_GET['ans']!=1) $insured_=false; else $insured_=true;

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));

$insured=false;
if ($insrow['bet_amount']!=0 && $player['balance']>=($insrow['bet_amount']/2) && $insured_==true) {
  if (db_query("UPDATE `players` SET `balance`=ROUND((`balance`-($insrow[bet_amount]/2)),8) WHERE `id`=$player[id] LIMIT 1"))
    $insured=true;
}

$tmp_table_s=generateHash(6);

db_query("CREATE TEMPORARY TABLE `temp_table_$tmp_table_s` ENGINE=InnoDB
             SELECT * FROM `insurance_process` WHERE `id`=$insrow[id];
            ");

db_query("UPDATE `temp_table_$tmp_table_s` SET `id`=NULL");
db_query("INSERT INTO `games` SELECT * FROM `temp_table_$tmp_table_s`");

$gameID=db_last_insert_id();

db_query("DROP TABLE `temp_table_$tmp_table_s`");
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
      db_query("UPDATE `players` SET `balance`=ROUND((`balance`+$wager),8) WHERE `id`=$player[id] LIMIT 1");
  }
  else {    
    db_query("UPDATE `games` SET `ended`=1,`winner`='dealer' WHERE `id`=$gameID LIMIT 1");
    $winner='dealer';
    $data['winner']='dealer';
    playerWon($player['id'],$gameID,$wager,$dealer_deck,'lose',true,serialize($final_shuffle));
    if ($insured)
      db_query("UPDATE `players` SET `balance`=ROUND((`balance`+$wager),8) WHERE `id`=$player[id] LIMIT 1");
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
  db_query("UPDATE `games` SET `insurance_process`=0 WHERE `player`=$player[id]");
  db_query("UPDATE `games` SET `insurance_process`=1 WHERE `id`=$gameID");
}

echo json_encode(array('error'=>'no'));


?>
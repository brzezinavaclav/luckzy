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
include '../../inc/db-conf.php';
include '../../inc/functions.php';
include __DIR__.'/../../inc/db_functions.php';


if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"))==0) exit();


$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));

validateAccess($player['id']);

$settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

$gD_q=db_query("SELECT * FROM `games` WHERE `ended`=0 AND `player`=$player[id] LIMIT 1");
if (db_num_rows($gD_q)==0) exit();

$gameData=db_fetch_array($gD_q);

$dealer['cards']=array();
foreach (unserialize($gameData['dealer_deck']) as $card) {
  $dealer['cards'][]=explode('_',$card);
}
$dealer['cards'][1][0]='-';
$dealer['cards'][1][1]='-';

$player_['cards']=array();
foreach (unserialize($gameData['player_deck']) as $card) {
  $player_['cards'][]=explode('_',$card);
}

$playerSums=implode(',',getSums(unserialize($gameData['player_deck'])));
$dealerSums=implode(',',getSums(unserialize($gameData['dealer_deck'])));
$data['mark']='-';
if ($gameData['player_deck_2']!='') {
  $player_['cards2']=array();
  foreach (unserialize($gameData['player_deck_2']) as $card) {
    $player_['cards2'][]=explode('_',$card);
  }
  array_splice($player_['cards'],1,0,array($player_['cards2'][0]));
  unset($player_['cards2'][0]);
  $player_['cards2']=array_values($player_['cards2']);

  $playerSums2=implode(',',getSums(unserialize($gameData['player_deck_2'])));

  $data['mark']=1;
  if ($gameData['player_deck_stand']==1) {
    $data['mark']=2;
  }
}
else $playerSums2='-';


echo json_encode(array('bet_amount'=>$gameData['bet_amount'],'dealer'=>$dealer,'player'=>$player_,'accessable'=>$gameData['accessable_actions'],'sums'=>array('player'=>$playerSums,'player2'=>$playerSums2), 'data'=>array('mark' => $data['mark'])));


?>

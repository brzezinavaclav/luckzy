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

maintenance();
if (!logged()){
  echo json_encode(array('error'=>'yes','message'=>'Please sign in to send messages'));
  exit();
}
if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"))==0) exit();
$player=db_fetch_array(db_query("SELECT `id` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));




$settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

if ($settings['chat_enable']==0) exit();

if (empty($_GET['data'])) {
  echo json_encode(array('error'=>'yes','content'=>'nodata'));
  exit();
}


$alone=true;
$lastTen=db_query("SELECT * FROM `chat` ORDER BY `time` DESC LIMIT 10 WHERE `room`=".$_COOKIE['chat_room']);
if (db_num_rows($lastTen)<10) $alone=false;
else {
  while ($each=db_fetch_array($lastTen)) {
    if ($each['sender']!=$player['id']) {
      $alone=false;
      break;
    }
  }
}

if ($alone) {
  echo json_encode(array('error'=>'yes','message'=>'You can\'t post more than 10 messages in a row .'));
  exit();
}

if($_COOKIE['pm']) $where = "for";
else $where = "room";

db_query("INSERT INTO `chat` (`sender`,`content`,`$where`) VALUES ($player[id],'".substr(prot($_GET['data']),0,200)."',".$_COOKIE['chat_room'].")");

echo json_encode(array('error'=>'no'));
?>

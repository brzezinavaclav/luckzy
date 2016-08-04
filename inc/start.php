<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/

if (!isset($init)) exit();

include __DIR__.'/wallet_driver.php';
include __DIR__.'/db-conf.php';
include __DIR__.'/db_functions.php';
include __DIR__.'/functions.php';

error_reporting(E_ALL & ~E_NOTICE);


if (!isset($conf_c)) {
  header('Location: ./install/');
  exit();
}

if (logged()) {
  $player = db_fetch_array(db_query("SELECT * FROM `players` WHERE `id`=".$_SESSION['user_id']));
  $unique = $player['hash'];
}

else {
  if (!empty($_COOKIE['unique_S_']) && db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='" . $_COOKIE['unique_S_'] . "' LIMIT 1")) != 0) {
    $player = db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='" . $_COOKIE['unique_S_'] . "' LIMIT 1"));
    $unique = $_COOKIE['unique_S_'];
  } else {
    newPlayer();
    header('Location: ./');
    exit();
  }
}


$playingGame=false;
$endedOnInit=false;
if (db_num_rows(db_query("SELECT `id` FROM `games` WHERE `ended`=0 AND `player`=$player[id] LIMIT 1"))!=0)
  $playingGame=true;
if (db_num_rows(db_query("SELECT `id` FROM `games` WHERE `ended`=1 AND `player`=$player[id] AND `insurance_process`=1 LIMIT 1"))!=0)
  $endedOnInit=true;


if(!isset($_COOKIE['game'])){
  setcookie('game', 'blackjack',(time()+60*60*24*365*5),'/');
  header('Location: ./');
}

$url = $_SERVER["REQUEST_URI"];
$page = parse_url($url, PHP_URL_QUERY);
if(empty($page)) $page = 'blackjack';

if($page == 'blackjack' || $page == 'slots' || $page == 'dice')  $game = $page;
else $game = false;


$settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));

$client_seed = $player['client_seed'];
$last_client_seed = $player['last_client_seed'];

if($game == "dice"){
  $server_seed = $player['dice_seed'];
  $last_server_seed = $player['last_dice_seed'];
  $last_result = $player['dice_last_result'];
}
else if($game == "slots"){
  $server_seed = slotsSeedExport($player['slots_seed']);
  $last_server_seed = slotsSeedExport($player['last_slots_seed']);
  $last_result = $player['slots_last_result'];
}
else if($game == "blackjack"){
  $server_seed = stringify_shuffle($player['initial_shuffle']);
  $last_server_seed = stringify_shuffle($player['last_initial_shuffle']);
  $last_result = stringify_shuffle($player['last_final_shuffle']);
}

if ($settings['maintenance']) {
  include __DIR__.'/maintenance.php';
  exit();
}
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

if(!isset($_COOKIE['game'])){
  setcookie('game', 'blackjack',(time()+60*60*24*365*5),'/');
  header('Location: ./');
}
$game = $_COOKIE['game'];
$settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));


if ($settings['maintenance']) {
  include __DIR__.'/maintenance.php';
  exit();
}
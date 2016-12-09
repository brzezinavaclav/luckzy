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

if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"))==0) exit();

maintenance();

if (empty($_GET['seed']) || (int)$_GET['seed']==0) {
  echo json_encode(array('error'=>'yes','message'=>'This must be a number.'));
  exit();
}
if(strlen((string)$_GET['seed']) > 32){
  echo json_encode(array('error'=>'yes','message'=>'Number can\'t be longer than 32 characters'));
  exit();
}

$repaired=(int)$_GET['seed'];

db_query("UPDATE `players` SET `client_seed`=".$repaired." WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1");

echo json_encode(array('error'=>'no'));

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

if (!logged())exit();
maintenance();

if(db_query("UPDATE `players` SET `chat_status`=".prot($_GET['status'])." WHERE `hash`='".$_COOKIE['unique_S_']."'") != false) echo json_encode(array('error' => 'no'));
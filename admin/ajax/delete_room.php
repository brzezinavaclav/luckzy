<?php
/*
 *  © CoinSlots
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.
*/


header('X-Frame-Options: DENY');

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

if (!isset($_SESSION['logged_']) || $_SESSION['logged_']!==true) exit();

if (empty($_GET['id']) || db_num_rows(db_query("SELECT `id` FROM `chat_rooms` WHERE `id`='".prot($_GET['id'])."' LIMIT 1"))==0) exit();

db_query("DELETE FROM `chat_rooms` WHERE `id`=".prot($_GET['id']));
echo json_encode(array('error'=>'no'));
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


$chat_room = db_fetch_array(db_query("SELECT `name` FROM `chat_rooms` WHERE `id`=".prot($_GET['id'])." LIMIT 1"));
$chat_room = $chat_room['name'];
setcookie('chat_room', prot($_GET['id']),(time()+60*60*24*365*5),'/');

echo json_encode(array('error'=>'no', 'name'=> $chat_room));
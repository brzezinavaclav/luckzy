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

if (empty($_GET['_player'])  || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `id`='".prot($_GET['_player'])."' LIMIT 1"))==0) exit();

if(empty($_GET['e'])){
    echo json_encode(array('error'=>'User must have an email'));
    exit();
}

if(empty($_GET['u'])){
    echo json_encode(array('error'=>'Username can\'t be empty'));
    exit();
}

$player = db_fetch_array(db_query("SELECT `password` FROM `players` WHERE `id`='".prot($_GET['_player'])."' LIMIT 1"));
if($player['password'] != prot($_GET['p'])){
    $password = hash('sha256', $_GET['p']);
}
else $password = prot($_GET['p']);

db_query("UPDATE `players` SET `username`='".prot($_GET['u'])."', `email`='".prot($_GET['e'])."',`state`=".$_GET['s'].", `password`='$password' WHERE `id`='".prot($_GET['_player'])."' LIMIT 1");
echo json_encode(array('error'=>'no'));
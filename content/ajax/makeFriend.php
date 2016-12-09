<?php

error_reporting(0);
header('X-Frame-Options: DENY');

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

if (!logged())exit();
if(!isset($_GET['friend']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `username`='".$_COOKIE['unique_S_']."'")) != 1){
    echo json_encode(array('error'=>'yes', 'message'=>'Player not found'));
    exit();
}

$friend = db_fetch_array(db_query("SELECT `id` FROM `players` WHERE `username`='".$_COOKIE['unique_S_']."'"));

if(db_num_rows(db_query("SELECT `player` FROM `player_relations` WHERE `player`=".$_SESSION['user_id']." AND `friend`=".$friend['id'])) != 0){
    echo json_encode(array('error'=>'yes', 'message'=>'Request already sent'));
    exit();
}

if(db_query("INSERT INTO `player_relations` (`player`, `friend`, `relation`, `state`)  VALUES ('".$_SESSION['user_id']."'," .$friend['id'].",1,0)") != false) echo json_encode(array('error'=>'no'));
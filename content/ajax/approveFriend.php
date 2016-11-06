<?php

error_reporting(0);
header('X-Frame-Options: DENY');

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

if (!logged())exit();
if(!isset($_GET['friend']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `id`='".prot($_GET['friend'])."'")) != 1){
    echo json_encode(array('error'=>'yes', 'message'=>'Player not found'));
    exit();
}

if(db_query("UPDATE `player_relations` SET `state`=1 WHERE `player`=".$_SESSION['user_id']." AND `friend`=".prot($_GET['friend'])) == false){
    echo json_encode(array('error'=>'yes', 'message'=>'MySQL error'));
    exit();
}
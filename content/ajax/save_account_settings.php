<?php
/*
 *  © CoinSlots 
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.  
*/


header('X-Frame-Options: DENY');

error_reporting(0);
$init=true;
include '../../inc/db-conf.php';
include '../../inc/db_functions.php';
include '../../inc/functions.php';

if(!logged())exit();

$player = db_fetch_array(db_query("SELECT `password` FROM `players` WHERE `id`=".$_SESSION['user_id']));
$password = $player['password'];

if(empty($_POST['email']) || empty($_POST['username'])){
    echo json_encode(array('error' => 'yes', 'message' => 'Required field wasn\'t filled properly'));
    exit();
}

if(db_num_rows(db_query("SELECT `id` FROM `players` WHERE `username`='".prot($_POST['username'])."' AND `id`!=".$_SESSION['user_id']." LIMIT 1"))!=0){
    echo json_encode(array('error' => 'yes', 'message' => 'Username is already taken'));
    exit();
}

if(db_num_rows(db_query("SELECT `id` FROM `players` WHERE `email`='".prot($_POST['email'])."' AND `id`!=".$_SESSION['user_id']." LIMIT 1"))!=0){
    echo json_encode(array('error' => 'yes', 'message' => 'Email is already taken'));
    exit();
}

if($_POST['passwd'] != ''){
    if($_POST['passwd'] != $_POST['re_passwd']){
        echo json_encode(array('error' => 'yes', 'message' => 'Passwords don\'t match'));
        exit();
    }
    else{
        $password = hash('sha256', prot($_POST['passwd']));
    }
}
if (db_query("UPDATE `players` SET `password`='$password', `email`='".prot($_POST['email'])."', `username`='".prot($_POST['username'])."', `currency_preference`=".prot($_POST['currency_preference'])." WHERE `id`=" . $_SESSION['user_id']) == false) {
    echo json_encode(array('error' => 'yes', 'message' => 'Mysql error'));
}
else echo json_encode(array('error' => 'no'));
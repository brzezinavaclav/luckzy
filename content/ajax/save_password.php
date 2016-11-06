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

if($_POST['passwd'] != $_POST['re_passwd']){
    echo json_encode(array('error' => 'yes', 'message' => 'Passwords don\'t match'));
    exit();
}
if (db_query("UPDATE `players` SET `password`='" . hash('sha256', prot($_POST['passwd'])) . "', `password_reset_hash`='' WHERE `id`=" . prot($_POST['id'])) == false) {
    echo json_encode(array('error' => 'yes', 'message' => 'Mysql error'));
}
else echo json_encode(array('error' => 'no'));
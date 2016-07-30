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
if (!empty($_POST['username']) && !empty($_POST['passwd']) && !empty($_POST['re_passwd']) && $_POST['passwd'] == $_POST['re_passwd'] && db_num_rows(db_query("SELECT `id` FROM `players` WHERE `username`='".prot($_POST['username'])."' LIMIT 1"))==0) {
  if (db_query("UPDATE `players` SET `username`='" . prot($_POST['username']) . "',`password`='" . hash('sha256', $_POST['passwd'])."' WHERE `hash`='".$_COOKIE['unique_S_']."'") != false) {
    echo json_encode(array('error' => 'no'));
    exit();
  }
  else{
    echo json_encode(array('error' => 'yes', 'message' => 'Mysql error'));
    exit();
  }
}
echo json_encode(array('error' => 'yes', 'message' => 'Fields wasn\'t filled properly'));
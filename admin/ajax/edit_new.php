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

if (empty($_GET['_new']) || !is_numeric($_GET['_new']) || empty($_GET['con']) || db_num_rows(db_query("SELECT `id` FROM `news` WHERE `id`='".prot($_GET['_new'])."' LIMIT 1"))==0) exit();

db_query("UPDATE `news` SET `content`='".prot($_GET['con'])."' WHERE `id`='".prot($_GET['_new'])."' LIMIT 1");
echo json_encode(array('error'=>'no'));
?>
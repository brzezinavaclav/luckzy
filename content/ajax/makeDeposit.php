<?php


header('X-Frame-Options: DENY');


error_reporting(0);
$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/wallet_driver.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

maintenance();
if(!logged()) exit();

if(!isset($_GET['c'])) exit();
if($_GET['c'] != 'btc' && (!isset($_GET['amount']) || $_GET['amount'] == 0)){
    echo json_encode(array('error'=>'yes', 'message' => 'Invalid amount'));
    exit();
}
$settings = db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));
$player=db_fetch_array(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));
$received = 1;
if($_GET['c'] == 'btc') {
    $new_addr=walletRequest('getnewaddress');
    $received = 0;
}
else $new_addr = generateHash(30);
if (!db_query("INSERT INTO `deposits` (`player_id`,`address`,`currency`,`amount`,`coins_amount`,`received`, `ip`) VALUES ($player[id],'$new_addr', '".prot($_GET['c'])."','".prot($_GET['amount'])."', '".prot($_GET['amount']*$settings[$_GET['c'].'_rate'])."', $received, '".$_SERVER['REMOTE_ADDR']."')")) $new_addr='ERROR GENERATING ADDRESS';

echo json_encode(array('confirmed'=>$new_addr));

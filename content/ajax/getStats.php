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


if (empty($_GET['_unique']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"))==0) exit();

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".prot($_GET['_unique'])."' LIMIT 1"));


maintenance();

$settings=db_fetch_array(db_query("SELECT * FROM `system` WHERE `id`=1 LIMIT 1"));


$t_wagered = $settings['t_wagered'];
$p_wagered = $player['t_wagered'];



echo  json_encode(array(
                      
          'global' => array(
          
            'spins'     =>  '<b>'.db_num_rows(db_query("SELECT `id` FROM `spins` WHERE `bet_amount`!=0")).'</b>',
            'wagered'   =>  '<b>'.n_num($t_wagered, true).'</b> '.$settings['currency_sign']
          
          ),
          'player' => array(

            'spins'     =>  '<b>'.db_num_rows(db_query("SELECT `id` FROM `spins` WHERE `player`=$player[id]")).'</b>',
            'wagered'   =>  '<b>'.n_num($p_wagered, true).'</b> '.$settings['currency_sign']
          
          )
      
      
      ));
?>
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


if (empty($_COOKIE['unique_S_']) || db_num_rows(db_query("SELECT `id` FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"))==0) exit();

$player=db_fetch_array(db_query("SELECT * FROM `players` WHERE `hash`='".$_COOKIE['unique_S_']."' LIMIT 1"));


maintenance();


echo  json_encode(array(
                      
          'global' => array(
          
            'spins'     =>  '<b>'.get_count().'</b>',
            'wagered'   =>  '<b>'.get_wagered().'</b> Coins'
          
          ),
          'player' => array(

            'spins'     =>  '<b>'.get_count($player[id]).'</b>',
            'wagered'   =>  '<b>'.get_wagered($player[id]).'</b> Coins'
          
          )
      
      
      ));
?>
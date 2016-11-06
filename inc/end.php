<?php
/*
 *  © CoinSlots
 *  Demo: http://www.btcircle.com/coinslots
 *  Please do not copy or redistribute.
 *  More licences we sell, more products we develop in the future.
*/

if (!isset($init)) exit();
db_query("UPDATE `players` SET `time_last_active`=NOW(),`lastip`='".$_SERVER['REMOTE_ADDR']."' WHERE `id`=$player[id] LIMIT 1");
db_query("DELETE FROM `players` WHERE `time_last_Active` < NOW()-INTERVAL 1 DAY AND `password`=''");
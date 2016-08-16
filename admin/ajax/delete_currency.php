<?php


header('X-Frame-Options: DENY');


$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';
if (!isset($_SESSION['logged_']) || $_SESSION['logged_']!==true) exit();

if (empty($_GET['currency']) || db_num_rows(db_query("SELECT `id` FROM `currencies` WHERE `id`=".prot($_GET['currency'])." LIMIT 1"))==0) exit();

db_query("DELETE FROM `currencies` WHERE `id`='".prot($_GET['currency'])."' LIMIT 1");
echo json_encode(array('error'=>'no'));
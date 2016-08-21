<?php

error_reporting(0);
header('X-Frame-Options: DENY');

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

if (!logged())exit();

echo json_encode(array('error' => 'no', 'pms' => get_pms()));
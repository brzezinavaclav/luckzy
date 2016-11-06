<?php

header('X-Frame-Options: DENY');

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

echo json_encode(array('error'=>'no','won_last'=>last_won('1 DAY'),'biggest'=>biggest_win()));
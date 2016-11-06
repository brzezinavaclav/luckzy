<?php

error_reporting(0);
header('X-Frame-Options: DENY');

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

if (!logged())exit();

echo json_encode(array('error' => 'no', 'friend_count' => count_friends(), 'online_count' => count_friends(1), 'offline_count' => count_friends(0), 'ignored_count' => count_friends(-1), 'requests_count' => count_friends(10), 'online_friends' => get_friends(1), 'offline_friends' => get_friends(0), 'ignored_friends' => get_friends(-1), 'friend_requests' => get_friends(10)));
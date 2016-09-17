<?php

header('X-Frame-Options: DENY');

error_reporting(0);
$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';
include '../../inc/phpmailer/PHPMailerAutoload.php';


if(db_num_rows(db_query("SELECT `id` FROM `players` WHERE `email`='".prot($_GET['email'])."' LIMIT 1"))==0){
    echo json_encode(array('error' => 'yes', 'message' => 'Email not found'));
    exit();
}

do $password_reset_hash = generateHash(32);
while (db_num_rows(db_query("SELECT `password_reset_hash` FROM `players` WHERE `password_reset_hash`='$password_reset_hash' LIMIT 1")) != 0);

$actual_link = "http://".$settings['url']."?reset=" . $password_reset_hash;

if(!send_mail($_GET['email'], 'Reset password email', 'Click this link to reset your password. <a href="'.$actual_link.'">'.$actual_link .'</a>')) {
    echo json_encode(array('error' => 'yes', 'message' => 'Password reset email couldn\'t be sent.'));
    exit();
}

$player = db_fetch_array(db_query("SELECT `id` FROM `players` WHERE `email`='".prot($_GET['email'])."' LIMIT 1"));

if (db_query("UPDATE `players` SET `password_reset_hash`='$password_reset_hash' WHERE `id`=" . $player['id']) == false) {
    echo json_encode(array('error' => 'yes', 'message' => 'Mysql error'));
    exit();
}

echo json_encode(array('error'=>'no'));
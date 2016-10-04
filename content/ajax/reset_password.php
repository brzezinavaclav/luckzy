<?php

header('X-Frame-Options: DENY');

error_reporting(0);
$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';


if(db_num_rows(db_query("SELECT `id` FROM `players` WHERE `email`='".prot($_GET['email'])."' LIMIT 1"))==0){
    echo json_encode(array('error' => 'yes', 'message' => 'Email not found'));
    exit();
}

do $password_reset_hash = generateHash(32);
while (db_num_rows(db_query("SELECT `password_reset_hash` FROM `players` WHERE `password_reset_hash`='$password_reset_hash' LIMIT 1")) != 0);

$actual_link = "http://".$settings['url']."?reset=" . $password_reset_hash;

if(!send_mail($_GET['email'], 'Reset password email', '<h1>Welcome to Luckzy.com</h1><br>Reset your password by clicking the button bellow.<br><br><a href="#" style="border-bottom: 2px solid #278C0B; border-radius: 3px; background: #599b47;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:13px;font-weight:bold;line-height:33px;text-align:center;text-decoration:none;width:130px;-webkit-text-size-adjust:none;">Activate!</a><br><br>')) {
    echo json_encode(array('error' => 'yes', 'message' => 'Password reset email couldn\'t be sent.'));
    exit();
}

$player = db_fetch_array(db_query("SELECT `id` FROM `players` WHERE `email`='".prot($_GET['email'])."' LIMIT 1"));

if (db_query("UPDATE `players` SET `password_reset_hash`='$password_reset_hash' WHERE `id`=" . $player['id']) == false) {
    echo json_encode(array('error' => 'yes', 'message' => 'Mysql error'));
    exit();
}

echo json_encode(array('error'=>'no'));
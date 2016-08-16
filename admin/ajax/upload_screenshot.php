<?php

header('X-Frame-Options: DENY');

$init=true;
include __DIR__.'/../../inc/db-conf.php';
include __DIR__.'/../../inc/db_functions.php';
include __DIR__.'/../../inc/functions.php';

if (!isset($_SESSION['logged_']) || $_SESSION['logged_']!==true) exit();

$settings = db_fetch_array(db_query("SELECT * FROM `system` LIMIT 1"));

if(!is_array($_FILES) || !isset($_GET['tid'])) exit();
    $files = array();
    for ($i = 0; $i < count($_FILES['file']['name']); $i++){
        if(is_uploaded_file($_FILES['file']['tmp_name'][$i])) {
            $sourcePath = $_FILES['file']['tmp_name'][$i];
            $name = substr($_FILES['file']['name'][$i], 0, strpos($_FILES['file']['name'][$i], '.')) . substr($_FILES['file']['name'][$i],strpos($_FILES['file']['name'][$i], '.'));
            $count = 0;
            while(db_num_rows(db_query("SELECT `name` FROM `screenshots` WHERE `name`='$name'")) != 0){
                $name = substr($_FILES['file']['name'][$i], 0, strpos($_FILES['file']['name'][$i], '.')) . "($count)" . substr($_FILES['file']['name'][$i],strpos($_FILES['file']['name'][$i], '.'));
                $count++;
            }
            $targetPath = "../screenshots/$name";
            if(!move_uploaded_file($sourcePath,$targetPath)) {
                echo json_encode(array('error' => 'yes'));
                exit();
            }
            $path = "http://".$settings['url']."/admin/screenshots/".$name;
            db_query("INSERT INTO `screenshots` (`tid`, `name`, `path`) VALUES(".$_GET['tid'].", '$name', '$path')");
        }
    }
echo json_encode(array('error' => 'no'));
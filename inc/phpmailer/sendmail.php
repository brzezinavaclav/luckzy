<?php
/*

*/
if(isset($_POST['name'])){
    require 'PHPMailerAutoload.php';

    $mail = new PHPMailer;


    $mail->setFrom($_POST['email'], $_POST['name']);
    $mail->addAddress('obchod@stavebninykachlik.cz', 'Radim Šatný');     // Add a recipient

    $mail->CharSet = 'UTF-8';

    $mail->Subject = 'Zpráva z formuláře na webu';
    $mail->Body    = $_POST['message'];

    if(!$mail->send()) {
        echo json_encode(array("message" => "Odeslání selhalo. Mailer Error: ". $mail->ErrorInfo));
    } else {
        echo json_encode(array("message" => "Zpráva odeslána"));
    }
}
else echo json_encode(array("message" => "Zpráva odeslána"));
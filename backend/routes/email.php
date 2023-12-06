<?php

require_once('vendor/autoload.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

Flight::route("POST /email/contact", function () {
    $email = Flight::request()->data["correoElectronico"];
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = "nicoleflorestorres27@gmail.com";
    $mail->Password = "bapy xxuj lroe ysxn";
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;
    $mail->setFrom("nicoleflorestorres27@gmail.com");
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = "Acerca de Jewernico";
    $mail->Body = "Hemos recibido tu mensaje, pronto nos pondremos en contacto contigo";
    $mail->send();
});
?>
<?php

namespace Acme\Jewernico\Controller\API;

use Flight;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailCoupon {
    public static function send() {
        $data = Flight::request()->data->getData();

        // Obtener el contenido del correo desde el archivo email.html
        $contenido_correo = file_get_contents("../app/resources/templates/email.html");

        // Crear instancia de PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Configurar el servidor SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; 
            $mail->SMTPAuth   = true;
            $mail->Username   = 'nicoleflorestorres27@gmail.com'; 
            $mail->Password   = 'bapy xxuj lroe ysxn'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = function ($str, $level) {
            // Puedes registrar o imprimir los mensajes de depuración aquí
            error_log("$str\n");
            };
            $mail->isHTML(true);

            // Configurar el remitente y el destinatario
            $mail->setFrom('nicoleflorestorres27@gmail.com', 'Jewernico');
            $mail->addAddress($data['solicitante']);

            // Configurar el asunto y el cuerpo del correo
            $mail->Subject = 'Cupón de descuento Jewernico';
            $mail->Body    = $contenido_correo;

            // Enviar el correo
            $mail->send();

            // Devolver una respuesta exitosa
            Flight::json(['success' => true, 'message' => 'Correo enviado con éxito']);
        } catch (Exception $e) {
            // Manejar errores en el envío del correo
            Flight::json(['success' => false, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
        }
    }
}

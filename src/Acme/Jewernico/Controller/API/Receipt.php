<?php

namespace Acme\Jewernico\Controller\API;

use Flight;
use PHPHtmlParser\Dom\HtmlNode;

class Receipt
{
    public static function send()
    {
        $data = Flight::request()->data->getData();

        $email = $_SESSION["user"]->getEmail();
        $payment_method = $data["metodoPago"];
        $address = $data["direccion"];
        $shipping = $data["envio"];
        $tax = $data["impuesto"];
        $coupon = $data["cupon"];
        $subtotal = $data["subtotal"];
        $products = $data["productos"];

        $receipt = new \Acme\Jewernico\Command\Receipt($payment_method, $address, $shipping, $tax, $coupon, $subtotal, $products);

        $mail = new \Acme\Jewernico\Command\Email($email, $receipt->get(), "Gracias por tu compra");
        try {
            $mail->send();
        } catch (\Exception $e) {
            Flight::json(["error" => "Error al enviar el correo"], 500);
            return;
        }
    }

    public static function pdf()
    {
        $data = Flight::request()->data->getData();

        $payment_method = $data["metodoPago"];
        $address = $data["direccion"];
        $shipping = $data["envio"];
        $tax = $data["impuesto"];
        $coupon = $data["cupon"];
        $subtotal = $data["subtotal"];
        $products = $data["productos"];

        $receipt = new \Acme\Jewernico\Command\Receipt($payment_method, $address, $shipping, $tax, $coupon, $subtotal, $products);
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="receipt.pdf"');
        $pdf = new \Dompdf\Dompdf();
        $pdf->loadHtml($receipt->get());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        $pdf->stream();
    }

}

?>
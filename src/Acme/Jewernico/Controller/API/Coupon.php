<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Coupon {
    public static function get($coupon) {
        $couponDb = \Acme\Jewernico\Command\Database::findCoupon($coupon);
        if (empty($couponDb)) {
            Flight::json(["error" => "Cupón no encontrado"], 404);
            return;
        }
        Flight::json(["data" => $couponDb], 200);
    }
}


?>
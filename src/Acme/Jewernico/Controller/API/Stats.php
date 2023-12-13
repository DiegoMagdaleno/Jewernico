<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Stats {
    public static function salesByCategory() {
        Flight::json(["data" => \Acme\Jewernico\Command\Database::getSalesOfEachCategory()], 200);
    }

    public static function salesByMonth() {
        Flight::json(["data" => \Acme\Jewernico\Command\Database::getSalesOfEachMonth()], 200);
    }
}

?>
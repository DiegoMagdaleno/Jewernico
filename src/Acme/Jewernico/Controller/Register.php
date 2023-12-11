<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Register
{
    public static function load()
    {
        $questions = \Acme\Jewernico\Command\Database::getSecurityQuestions();
        Flight::view()->display("register.twig", ["questions" => $questions]);
    }
}


?>
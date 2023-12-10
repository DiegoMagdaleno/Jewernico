<?php

namespace Acme\Jewernico\Controller;

use Flight;

class Captcha {
    public static function create() {
        $captcha = new \Gregwar\Captcha\CaptchaBuilder;
        $captcha->build();
        $_SESSION['phrase'] = $captcha->getPhrase();
        header('Content-type: image/jpeg');
        $captcha->output();
    }
}



?>
<?php

require_once('vendor/autoload.php');

Flight::route("GET /captcha", function () {
    $captcha = new \Gregwar\Captcha\CaptchaBuilder();
    $captcha->build();
    $phrase = $captcha->getPhrase();
    $_SESSION["phrase"] = $phrase;
    header("Content-type: image/jpeg");
    $captcha->output();
});

?>
<?php

session_start();
require_once "vendor/autoload.php";
require_once "tokens.php";

use Firebase\JWT\ExpiredException;

Flight::route("POST /refresh_token", function () {
    $data = Flight::request()->data->getData();
    $tokenCookie = Flight::request()->cookies->token;

    if ($data['captcha'] != $_SESSION['phrase']) {
        Flight::json(
            array(
                "status" => 403,
                "message" => "Captcha incorrecto"
            ),
            403
        );
        return;
    }

    try {
        $token = checkToken($tokenCookie, "ALGUNA_CLAVE_SECRETA");
        // Nice! lets get the refresh token
        $refreshTokenReq = $data["refreshToken"];
        try {
            checkToken($refreshTokenReq, "ALGUNA_CLAVE_SECRETA");
            // Perfect lets create a new token
            $newToken = generateToken("ALGUNA_CLAVE_SECRETA", $token->data, strtotime("now") + 60 * 60 * 24);
            Flight::json(
                array(
                    "token" => $newToken
                )
            );
        } catch (ExpiredException $e) {
            Flight::json(
                array(
                    "status" => 401,
                    "message" => "Token de refresco expirado"
                ),
                401
            );
        }

    } catch (ExpiredException $e) {
        Flight::json(
            array(
                "status" => 401,
                "message" => "Token expirado"
            ),
            401
        );
    }
});

?>
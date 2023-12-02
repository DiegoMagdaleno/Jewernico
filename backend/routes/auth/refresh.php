<?php

require_once "vendor/autoload.php";
require_once "tokens.php";

use Firebase\JWT\ExpiredException;

Flight::route("POST /refresh_token", function () {
    $data = Flight::request()->data->getData();
    $tokenCookie = Flight::request()->cookies->token;

    try {
        $token = checkToken($tokenCookie, "ALGUNA_LLAVE_SECRETA");
        // Nice! lets get the refresh token
        $refreshTokenReq = $data["refreshToken"];
        try {
            checkToken($refreshTokenReq, "ALGUNA_LLAVE_SECRETA");
            // Perfect lets create a new token
            $newToken = generateToken("ALGUNA_LLAVE_SECRETA", $token->data, strtotime("now") + 3600);
            Flight::json(
                array(
                    "token" => $newToken
                )
            );
        } catch (ExpiredException $e) {
            Flight::json(
                array(
                    "status" => 401,
                    "message" => "Token expirado"
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
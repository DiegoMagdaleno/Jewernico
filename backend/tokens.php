<?php

require_once "vendor/autoload.php";

use Firebase\JWT\JWT;

function generateToken($key, $data, $exp) {
    $data = array(
        "data" => $data,
        "exp" => $exp,
        "key" => $key
    );
    return JWT::encode($data, $key, "HS256");
}

function checkToken($token, $key) {
    $tokenInfo = JWT::decode($token, new Key($key, "HS256"));

    $db = Flight::db();

    $query = $db->prepare("SELECT * FROM usuario WHERE Id = :id");
    $query->execute(array(
        ":id" => $tokenInfo->data->id,
    )
    );

    if ($query->rowCount() > 0) {
        return $tokenInfo;
    } else {
        return NULL;
    }
}
?>
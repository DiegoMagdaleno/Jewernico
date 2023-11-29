<?php

require_once 'vendor/autoload.php';
require_once 'util.php';

Flight::route("GET /users", function(){
    $token = validateToken();

    if (!$token) {
        Flight::json(
            array(
                "status" => 403,
                "message" => "No autorizado"
            ),
            403
        );
    } else {
        $permissionLevel = $token->data->nivelPermisos;
        if ($permissionLevel != 2) {
            Flight::json(
                array(
                    "status" => 403,
                    "message" => "No autorizado"
                ),
                403
            );
        } else {
            $db = Flight::db();
            $query = $db->prepare("SELECT * FROM usuario");
            $query->execute();
            $users = $query->fetchAll();
            Flight::json($users);
        }
    }
});

?>
<?php

require_once("vendor/autoload.php");

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function arrayToHTMLTable($arrayOfAssocArrays)
{
    if (empty($arrayOfAssocArrays)) {
        return '<p>No data to display</p>';
    }

    $html = '<table border="1">';

    // Generate table headers using keys from the first associative array
    $html .= '<thead><tr>';
    $firstRow = reset($arrayOfAssocArrays);
    foreach (array_keys($firstRow) as $key) {
        $html .= '<th>' . htmlspecialchars($key) . '</th>';
    }
    $html .= '</tr></thead>';

    $html .= '<tbody>';
    foreach ($arrayOfAssocArrays as $row) {
        $html .= '<tr>';
        foreach ($row as $value) {
            $html .= '<td>' . htmlspecialchars($value) . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
}

function getToken()
{
    $headers = apache_request_headers();

    if (isset($headers["Authorization"])) {
        $authorization = $headers["Authorization"];
        $authorizationArray = explode(" ", $authorization);

        if (count($authorizationArray) == 2 && $authorizationArray[0] == "Bearer") {
            return JWT::decode($authorizationArray[1], new Key("ALGUNA_CLAVE_SECRETA", "HS256"));
        } else {
            return NULL;
        }
    } else {
        return NULL;
    }
}

function validateToken()
{
    $info = getToken();
    if ($info == NULL) {
        return false;
    }
    $db = Flight::db();

    $query = $db->prepare("SELECT * FROM usuario WHERE Id = :id");
    $query->execute(
        array(
            ":id" => $info->data->id,
        )
    );
    $rows = $query->fetchColumn();
    if ($rows == 0) {
        return false;
    } else {
        return $info;
    }
}
?>
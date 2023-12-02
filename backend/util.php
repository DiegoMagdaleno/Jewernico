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
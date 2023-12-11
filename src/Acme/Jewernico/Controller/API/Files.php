<?php

namespace Acme\Jewernico\Controller\API;

use Flight;

class Files
{
    public static function upload()
    {
        $ds = DIRECTORY_SEPARATOR;
        $files = Flight::request()->files;

        $tmp_file = $files["file"]["tmp_name"];

        $target_path = realpath(__DIR__ . $ds . ".." . $ds . ".." . $ds . ".." . $ds . ".." . $ds . ".." . $ds . "public" . $ds . "resources" . $ds . "store" . $ds . "images");

        $target_file = $target_path . $ds . $files["file"]["name"];

        $upload = move_uploaded_file($tmp_file, $target_file);

        if ($upload) {
            Flight::json(["path" => "/resources/store/images/" . $files["file"]["name"]], 200);
        } else {
            Flight::json(["error" => "Error uploading file"], 400);
        }
    }
}

?>
<?php
// -------------------------------------------------- //
// INIT
// -------------------------------------------------- //

require_once(__DIR__ . "/../vendor/autoload.php");
require_once(__DIR__ . "/../app/config/config.php");
require_once(__DIR__ . "/../app/config/log_handlers.php");
require_once(__DIR__ . "/../app/logger.php");
require_once(__DIR__ . "/../app/config/routes.php");

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// -------------------------------------------------- //
// LOGIC
// -------------------------------------------------- //

// Setup flight vars
foreach (FLIGHT_SET_VARS as $key => $value) {
    Flight::set($key, $value);
}

$databaseUrl = "mysql:host=" . $_ENV["DATABASE_HOST"] . ";dbname=" . $_ENV["DATABASE_NAME"];

// Configure Database
Flight::register("db", "PDO", array($databaseUrl, $_ENV["DATABASE_USER"], $_ENV["DATABASE_PASSWORD"]), function ($db) {
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
});

// Configure Twig with Flight
$twig_loader = new \Twig\Loader\FilesystemLoader(Flight::get("flight.views.path"));

Flight::register("view", "Twig\Environment", [$twig_loader, TWIG_CONFIG], function ($twig) {
    if (DEBUG) {
        $twig->addExtension(new \Twig\Extension\DebugExtension());
    }
});

//Fecha de modificación
function get_last_modified_files($directory)
{
    $files = scandir($directory);
    $modified_files = [];

    foreach ($files as $file) {
        if (preg_match('/\.(twig|ts|php)$/', $file)) {
            $modified_files[] = [
                'filename' => $file,
                'modified_at' => filemtime($directory . '/' . $file),
            ];
        }
    }

    usort($modified_files, function ($a, $b) {
        return $b['modified_at'] - $a['modified_at'];
    });

    return $modified_files;
}

$modified_files = get_last_modified_files('../app');

$fecha_supongo = date('d/m/Y H:i:s', $modified_files[0]['modified_at']);
date_default_timezone_set('America/Mexico_City');

$GLOBALS['last_modified'] = $fecha_supongo;

// All object definitions loaded, lets start the session
session_start();

if (isset($_COOKIE["correoElectronico"]) && isset($_COOKIE["password"]) && !in_array("user", $_SESSION)) {
    $correoElectronico = $_COOKIE["correoElectronico"];
    $password = $_COOKIE["password"];
    
    $sql = "SELECT * FROM usuario WHERE CorreoElectronico = :correoElectronico";
    $stmt = Flight::db()->prepare($sql);
    $stmt->bindParam(':correoElectronico', $correoElectronico);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $logger->debug($row["Password"]);

    if ($row["Password"] == $password) {
        $_SESSION["user"] = new \Acme\Jewernico\Model\User($row["Id"], $row["Nombre"], $row["CorreoElectronico"], $row["Password"], $row["NivelPermisos"]);
        $_SESSION["cartCount"] = \Acme\Jewernico\Command\Database::getTotalCartItems($_SESSION["user"]->getId());
    }
}

// Make twig have access to session variables
Flight::view()->addGlobal('session', $_SESSION);

// Make twig have access to global variables
Flight::view()->addGlobal('global', $GLOBALS);

$GLOBALS['url'] = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';

// Lets go!
Flight::start();


?>
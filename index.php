<?php
session_start();
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('./');
$twig = new \Twig\Environment($loader);

$logueado = false;
$rol = "";

// Para comprobar que las variables de Session tienen contenido no nulo
if (isset($_SESSION['loggedin']) && isset($_SESSION['rol'])) {
    $logueado = $_SESSION['loggedin'];
    $rol = $_SESSION['rol'];
    echo $rol;
}

$template = $twig->load('index.html');
echo $template->render(['logueado' => $logueado, 'rol' => $rol]);

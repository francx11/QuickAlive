<?php
require_once "../bd/bd.php";
require "../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../frontend/admin/templates/');
$twig = new \Twig\Environment($loader);

$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';

if ($registradoRoot) {
    echo $twig->render('panelAdmin.html', []);
}

<?php
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionUsuarios');
$twig = new \Twig\Environment($loader);

$bd = new BD();
header('Content-Type: application/json');

$bd->iniciarConexion();

$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';

$usuariosBuscados = [];

if ($registradoRoot && isset($_POST['nickNameBuscado'])) {
    $nickName = $_POST['nickNameBuscado'];
    $usuariosBuscados = $bd->buscarCoincidenciasUsuario($nickName);

    echo json_encode($usuariosBuscados);

}

$bd->cerrarConexion();

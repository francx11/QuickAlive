<?php
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionActividades');
$twig = new \Twig\Environment($loader);

$bd = new BD();
header('Content-Type: application/json');

$bd->iniciarConexion();

$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
$logueado = $_SESSION['loggedin'];

$actividadesBuscadas = [];

if (isset($_POST['nombreActividadBuscado'])) {
    $nombreActividad = $_POST['nombreActividadBuscado'];
    $actividadesBuscadas = $bd->buscarCoincidenciasActividad($nombreActividad);

}

echo json_encode($actividadesBuscadas);

$bd->cerrarConexion();

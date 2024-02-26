<?php
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionPreferencias');
$twig = new \Twig\Environment($loader);

$bd = new BD();
header('Content-Type: application/json');

$bd->iniciarConexion();

$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';

$tipoPreferenciasBuscadas = [];

if ($registradoRoot && isset($_POST['tipoPreferenciaBuscado'])) {
    $tipoPreferencia = $_POST['tipoPreferenciaBuscado'];
    $tipoPreferenciasBuscadas = $bd->buscarCoincidenciasTipoPreferencias($tipoPreferencia);

    echo json_encode($tipoPreferenciasBuscadas);
}

$bd->cerrarConexion();

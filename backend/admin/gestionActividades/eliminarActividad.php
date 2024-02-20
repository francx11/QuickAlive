<?php
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$bd = new BD();
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
$idUsuario = -1;

if ($registradoRoot) {
    $bd->iniciarConexion();

    if (isset($_GET['id'])) {
        $idUsuario = $_GET['id'];
    }

    if ($bd->eliminarActividad($idUsuario)) {
        header('Location: gestionActividades.php');
    } else {
        echo 'Error en la eliminación de la actividad';
    }

}

$bd->cerrarConexion();

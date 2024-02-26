<?php

require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$bd = new BD();
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
$logueado = $_SESSION['loggedin'];
$idTipoPreferencia = -1;

if ($registradoRoot) {
    $bd->iniciarConexion();

    if (isset($_GET['id'])) {
        $idTipoPreferencia = $_GET['id'];
    }

    if ($bd->eliminarTipoPreferencia($idTipoPreferencia)) {
        header('Location: gestionPreferencias.php');
    } else {
        echo 'Error en la eliminación de la preferencia';
    }

}

$bd->cerrarConexion();

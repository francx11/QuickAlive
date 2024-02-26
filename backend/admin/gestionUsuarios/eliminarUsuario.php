<?php

require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$bd = new BD();
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
$logueado = $_SESSION['loggedin'];
$idUsuario = -1;

if ($registradoRoot) {
    $bd->iniciarConexion();

    if (isset($_GET['id'])) {
        $idUsuario = $_GET['id'];
    }

    if ($bd->eliminarUsuario($idUsuario)) {
        header('Location: gestionUsuarios.php');
    } else {
        echo 'Error en la eliminación del usuario';
    }

}

$bd->cerrarConexion();

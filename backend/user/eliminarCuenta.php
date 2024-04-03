<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../vendor/autoload.php";

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {
    $bd = new BD();

    $idAEliminar = $_SESSION['idUsuario'];

    if ($bd->eliminarUsuario($idAEliminar)) {
        header("Location: ../api/sesiones/logout.php");
    } else {
        echo "Error al eliminar a un usuario";
    }

}

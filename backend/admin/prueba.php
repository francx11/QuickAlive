<?php
require_once "../bd/bd.php";

$bd = new BD();

// Obtén la conexión
$conexion = $bd->iniciarConexion();

// Comprueba si la conexión se realizó correctamente
if ($conexion->connect_errno) {
    echo "Fallo en la conexión: " . $conexion->connect_errno;
} else {
    echo "Conexión exitosa a la base de datos.";
}

// Cierra la conexión
$bd->cerrarConexion();

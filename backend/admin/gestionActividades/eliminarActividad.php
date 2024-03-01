<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../../vendor/autoload.php";

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();

// Crear una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Verificar si el usuario está registrado como 'root' y está logueado
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
// Obtener el estado de inicio de sesión del usuario
$logueado = $_SESSION['loggedin'];
// Establecer un valor predeterminado para el ID del usuario
$idUsuario = -1;

// Si el usuario está registrado como 'root'
if ($registradoRoot) {
    // Iniciar una conexión a la base de datos
    $bd->iniciarConexion();

    // Obtener el ID del usuario desde la consulta GET si está presente
    if (isset($_GET['id'])) {
        $idUsuario = $_GET['id'];
    }

    // Intentar eliminar la actividad correspondiente al ID proporcionado
    if ($bd->eliminarActividad($idUsuario)) {
        // Redirigir a la página de gestión de actividades después de eliminar la actividad
        header('Location: gestionActividades.php');
    } else {
        // Mostrar un mensaje de error si la eliminación de la actividad falla
        echo 'Error en la eliminación de la actividad';
    }

}

// Cerrar la conexión a la base de datos
$bd->cerrarConexion();

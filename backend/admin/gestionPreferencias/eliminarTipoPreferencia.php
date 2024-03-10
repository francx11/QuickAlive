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
// Inicializar el ID del tipo de preferencia como -1
$idTipoPreferencia = -1;

// Si el usuario está registrado como 'root'
if ($registradoRoot) {

    // Verificar si se proporcionó un ID de tipo de preferencia en la URL
    if (isset($_GET['id'])) {
        // Asignar el valor del ID de tipo de preferencia proporcionado
        $idTipoPreferencia = $_GET['id'];
    }

    // Intentar eliminar el tipo de preferencia con el ID proporcionado
    if ($bd->eliminarTipoPreferencia($idTipoPreferencia)) {
        // Redirigir a la página de gestión de preferencias si la eliminación fue exitosa
        header('Location: gestionPreferencias.php');
    } else {
        // Mostrar un mensaje de error si la eliminación falló
        echo 'Error en la eliminación de la preferencia';
    }

}

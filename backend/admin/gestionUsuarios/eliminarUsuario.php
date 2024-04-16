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
// Variable para almacenar el ID del usuario a eliminar (se inicializa en -1)
$idUsuario = -1;

// Si el usuario está registrado como 'root', proceder con la eliminación del usuario
if ($registradoRoot) {

    // Verificar si se proporcionó un ID de usuario a eliminar a través de la variable $_GET
    if (isset($_GET['id'])) {
        // Asignar el valor del ID de usuario proporcionado
        $idUsuario = $_GET['id'];
    }

    // Intentar eliminar el usuario con el ID especificado
    if ($bd->eliminarUsuario($idUsuario)) {
        // Redirigir a la página de gestión de usuarios después de la eliminación exitosa
        header('Location: gestionUsuarios.php');
    } else {
        // Si hubo un error durante la eliminación, mostrar un mensaje de error
        echo 'Error en la eliminación del usuario';
    }
}

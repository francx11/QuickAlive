<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../../vendor/autoload.php";

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();

// Crear un cargador de plantillas Twig para cargar plantillas desde un directorio específico
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionUsuarios');
// Crear una instancia de Twig para procesar las plantillas
$twig = new \Twig\Environment($loader);

// Crear una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Establecer el encabezado de la respuesta HTTP como JSON
header('Content-Type: application/json');

// Verificar si el usuario está registrado como 'root' y está logueado
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
// Obtener el estado de inicio de sesión del usuario
$logueado = $_SESSION['loggedin'];

// Array para almacenar los usuarios buscados
$usuariosBuscados = [];

// Si el usuario está registrado como 'root' y se ha enviado un nombre de usuario para buscar
if ($registradoRoot && isset($_POST['nickNameBuscado'])) {
    // Obtener el nombre de usuario enviado mediante POST
    $nickName = $_POST['nickNameBuscado'];
    // Buscar coincidencias de usuarios en la base de datos utilizando el nombre de usuario proporcionado
    $usuariosBuscados = $bd->buscarCoincidenciasUsuario($nickName);

    // Enviar la respuesta JSON con los usuarios encontrados
    echo json_encode($usuariosBuscados);
}

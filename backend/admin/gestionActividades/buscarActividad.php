<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../../vendor/autoload.php";

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionActividades');
$twig = new \Twig\Environment($loader);

// Crear una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Establecer el tipo de contenido de la respuesta HTTP como JSON
header('Content-Type: application/json');

// Verificar si el usuario está registrado como 'root' y está logueado
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
// Obtener el estado de inicio de sesión del usuario
$logueado = $_SESSION['loggedin'];

// Inicializar un array para almacenar las actividades buscadas
$actividadesBuscadas = [];

// Si se ha enviado una solicitud POST con el parámetro nombreActividadBuscado
if (isset($_POST['nombreActividadBuscado'])) {
    // Obtener el valor de nombreActividadBuscado de la solicitud POST
    $nombreActividad = $_POST['nombreActividadBuscado'];
    // Realizar una búsqueda de actividades en la base de datos que coincidan con el nombre proporcionado
    $actividadesBuscadas = $bd->buscarCoincidenciasActividad($nombreActividad);
}

// Convertir los resultados de la búsqueda en formato JSON y enviar la respuesta
echo json_encode($actividadesBuscadas);

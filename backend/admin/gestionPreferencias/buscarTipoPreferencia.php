<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../../vendor/autoload.php";

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();

// Crear una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();
// Establecer el encabezado de la respuesta como tipo de contenido JSON
header('Content-Type: application/json');

// Iniciar la conexión a la base de datos
$bd->iniciarConexion();

// Verificar si el usuario está registrado como 'root' y está logueado
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
// Obtener el estado de inicio de sesión del usuario
$logueado = $_SESSION['loggedin'];

// Inicializar un array para almacenar los tipos de preferencias buscados
$tipoPreferenciasBuscadas = [];

// Si el usuario está registrado como 'root' y se envió un tipo de preferencia para buscar
if ($registradoRoot && isset($_POST['tipoPreferenciaBuscado'])) {
    // Obtener el tipo de preferencia enviado desde el formulario
    $tipoPreferencia = $_POST['tipoPreferenciaBuscado'];
    // Realizar una búsqueda de coincidencias de tipo de preferencia en la base de datos
    $tipoPreferenciasBuscadas = $bd->buscarCoincidenciasTipoPreferencias($tipoPreferencia);

    // Convertir el array de resultados a formato JSON y enviarlo como respuesta
    echo json_encode($tipoPreferenciasBuscadas);
}

// Cerrar la conexión a la base de datos
$bd->cerrarConexion();

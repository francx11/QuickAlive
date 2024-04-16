<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../../vendor/autoload.php";

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();

// Configurar el cargador de plantillas Twig para cargar plantillas desde el directorio correspondiente
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionActividades');
$twig = new \Twig\Environment($loader);

// Verificar si el usuario está registrado como 'root' y está logueado
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
// Obtener el estado de inicio de sesión del usuario
$logueado = $_SESSION['loggedin'];

// Si el usuario está registrado como 'root'
if ($registradoRoot) {
    // Renderizar la plantilla 'gestionActividades.html' utilizando Twig y pasar la variable de inicio de sesión como contexto
    echo $twig->render('gestionActividades.html', ['logueado' => $logueado]);
}

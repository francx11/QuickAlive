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

// Obtiene el ID de la actividad de la solicitud GET, o establece -1 si no se proporciona
$idActividad = isset($_GET['id']) ? $_GET['id'] : -1;;

// Verificar si el usuario está registrado como 'root' y está logueado
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
// Obtener el estado de inicio de sesión del usuario
$logueado = $_SESSION['loggedin'];

// Si el usuario está registrado como 'root'
if ($registradoRoot) {

    //echo $idActividad;

    // Obtiene la galería de imágenes actual de la actividad
    $galeriaActual = $bd->getGaleriaActividad($idActividad);

    $categoriasActividad = $bd->getCategoriasActividad($idActividad);

    //echo var_dump($categoriasActividad);

    $tiposPreferencias = $bd->getAllTipoPreferencias();
    echo $twig->render('modificarActividad.html', ['logueado' => $logueado, 'tiposPreferencias' => $tiposPreferencias, 'idActividad' => $idActividad, 'imagenes' => $galeriaActual, 'categorias' => $categoriasActividad]);
}

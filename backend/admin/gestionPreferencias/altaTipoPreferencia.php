<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../../vendor/autoload.php";

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();

// Crear un cargador de plantillas Twig para cargar plantillas desde un directorio específico
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionPreferencias');
// Crear una instancia de Twig para procesar las plantillas
$twig = new \Twig\Environment($loader);

// Crear una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Verificar si el usuario está registrado como 'root' y está logueado
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
// Obtener el estado de inicio de sesión del usuario
$logueado = $_SESSION['loggedin'];

// Si el usuario está registrado como 'root'
if ($registradoRoot) {
    // Iniciar la conexión a la base de datos
    $bd->iniciarConexion();

    // Si se recibió una solicitud POST desde el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtener el tipo de preferencia enviado desde el formulario
        $tipoPreferencia = $_POST['tipoPreferencia'];

        // Insertar el nuevo tipo de preferencia en la base de datos
        if (!$bd->insertarTipoDePreferencia($tipoPreferencia)) {
            // Si hay un error en la inserción, mostrar un mensaje de error
            echo 'Error en la inserción de la preferencia';
        }

        // Redirigir a la misma página para evitar el reenvío del formulario al recargar la página
        header("Location: gestionPreferencias.php");
        exit(); // Finalizar el script para evitar la ejecución de código adicional
    }

    // Renderizar el formulario de alta de tipo de preferencia utilizando Twig y enviar la variable de sesión 'logueado'
    echo $twig->render('altaTipoPreferencia.html', ['logueado' => $logueado]);
}

// Cerrar la conexión a la base de datos
$bd->cerrarConexion();

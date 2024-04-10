<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/user/templates/gestionRegistro');
$twig = new \Twig\Environment($loader);

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {

    // Verificar si se ha recibido una solicitud POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener el ID del usuario y las preferencias enviadas desde la solicitud POST
        $idUsuario = $_POST["idUsuario"];
        $preferencias = $_POST["preferencias"];

        // Crear una instancia de la clase BD para interactuar con la base de datos
        $bd = new BD();

        // Actualizar las preferencias personales del usuario
        $bd->actualizarPreferenciasPersonales($idUsuario, $preferencias);

        // Redirigir al usuario a la página de éxito
        //header("Location: ");
        exit(); // Detener la ejecución del script
    } else {
        // Responder con un mensaje de error si no se recibió una solicitud POST válida
        http_response_code(400);
        echo "Error: Solicitud no válida";
    }

}

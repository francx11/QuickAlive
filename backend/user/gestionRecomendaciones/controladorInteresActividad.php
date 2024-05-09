<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {
    // Verificar si se ha recibido una solicitud GET
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Obtener el ID del usuario y el estado de la actividad enviados por GET
        $idUsuario = $_SESSION['idUsuario'];
        $idActividad = $_GET["idActividad"];
        $estado = $_GET["estado"];

        // Crear una instancia de la clase BD para interactuar con la base de datos
        $bd = new BD();

        // Obtener las categorías de la actividad
        $categoriasActividad = $bd->getCategoriasActividad($idActividad);

        // Obtener las preferencias del usuario
        $preferenciasUsuario = $bd->getPreferenciasUsuario($idUsuario);

        // Llamar a la función para actualizar los puntos de interés
        $bd->actualizarPuntosInteres($categoriasActividad, $preferenciasUsuario, $estado);

        // Ejemplo de respuesta
        $response = array(
            'status' => 'success',
            'message' => 'Actividad actualizada correctamente'
        );

        // Convertir el array a formato JSON
        echo json_encode($response);
    } else {
        // Responder con un mensaje de error si no se recibió una solicitud GET válida
        http_response_code(400);
        echo "Error: Solicitud no válida";
    }
} else {
    // Si el usuario no está logueado, redirigirlo al inicio de sesión u otra página de autenticación
    // header("Location: login.php");
    exit();
}

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
        $idUsuario = $_SESSION['idUsuario'];
        // Obtener los parámetros enviados por GET
        $nombreActividad = $_GET["nombreActividad"];
        $descripcion = $_GET["descripcion"];
        $urlRemota = $_GET["urlRemota"];
        $idApi = $_GET["idApi"];
        $fechaRealizacion = $_GET["fechaRealizacion"];
        $estado = $_GET["estado"];
        $duracion = 0;

        // Crear una instancia de la clase BD para interactuar con la base de datos
        $bd = new BD();

        // Insertar la actividad geolocalizable y obtener su ID
        $idActividadInsertada = $bd->insertarActividadGeolocalizable($nombreActividad, $descripcion, $duracion, $urlRemota, $idApi, $fechaRealizacion);

        // Si la inserción fue exitosa, proceder según el estado recibido
        if ($idActividadInsertada !== -1) {
            if ($estado === "aceptada") {
                // Realizar la actividad
                $bd->realizarActividad($idUsuario, $idActividadInsertada);

                // Modificar la fecha de realización
                $actividadGeo = $bd->getActividadGeolocalizable($idActividadInsertada);
                $fechaRealizacion = $actividadGeo->getFechaLimite();
                $bd->modificarFechaRealizacion($idUsuario, $idActividadInsertada, $fechaRealizacion);
            } elseif ($estado === "rechazada") {
                // Rechazar la actividad
                $bd->rechazarActividad($idUsuario, $idActividadInsertada);
            }

            // Responder con un mensaje de éxito
            $response = array(
                'status' => 'success',
                'message' => 'Actividad insertada correctamente'
            );

            // Convertir el array a formato JSON
            echo json_encode($response);
        } else {
            // Responder con un mensaje de error si la inserción falló
            $response = array(
                'status' => 'error',
                'message' => 'Error al insertar la actividad'
            );

            // Convertir el array a formato JSON
            echo json_encode($response);
        }
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

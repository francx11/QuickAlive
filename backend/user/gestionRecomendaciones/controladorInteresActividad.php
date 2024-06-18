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

        if (!empty($preferenciasUsuario)) {
            // Llamar a la función para actualizar los puntos de interés
            $bd->actualizarPuntosInteres($categoriasActividad, $preferenciasUsuario, $estado);
        }


        if ($estado === "aceptada") {
            $bd->realizarActividad($idUsuario, $idActividad);

            // Verificar si las preferencias del usuario están vacías
            if (empty($preferenciasUsuario)) {
                // Obtener los idTipoPreferencia de las categorías de la actividad
                $idTipoPreferenciasActividad = array_column($categoriasActividad, 'idTipoPreferencia');

                // Insertar las preferencias de la actividad para el usuario
                foreach ($idTipoPreferenciasActividad as $idTipoPreferencia) {
                    $nombreTipoPreferencia = $bd->obtenerNombreTipoPreferencia($idTipoPreferencia);
                    $bd->insertarPreferenciaPersonal($idUsuario, $nombreTipoPreferencia, $idTipoPreferencia);
                }
            } else {
                // Obtener los idTipoPreferencia del usuario
                $idTipoPreferenciasUsuario = array_column($preferenciasUsuario, 'idTipoPreferencia');

                // Comparar los idTipoPreferencia de las categorías de la actividad con los del usuario
                $idTipoPreferenciasFaltantes = array_diff(array_column($categoriasActividad, 'idTipoPreferencia'), $idTipoPreferenciasUsuario);

                // Insertar las preferencias faltantes para el usuario
                foreach ($idTipoPreferenciasFaltantes as $idTipoPreferencia) {
                    $nombreTipoPreferencia = $bd->obtenerNombreTipoPreferencia($idTipoPreferencia);
                    $bd->insertarPreferenciaPersonal($idUsuario, $nombreTipoPreferencia, $idTipoPreferencia);
                }
            }
        } else if ($estado === "rechazada") {
            $bd->rechazarActividad($idUsuario, $idActividad);
        }



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

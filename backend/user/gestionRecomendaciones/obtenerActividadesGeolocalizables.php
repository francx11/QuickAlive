<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';

// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el idUsuario está presente en la sesión
if (isset($_SESSION['idUsuario'])) {

    // Crear una instancia del objeto BD para interactuar con la base de datos
    $bd = new BD();

    // Obtener las actividades geolocalizables que están siendo realizadas o que han sido rechazadas
    $actividadesGeolocalizables = $bd->obtenerActividadesGeolocalizables();

    // Verificar si se obtuvieron actividades geolocalizables
    if ($actividadesGeolocalizables !== false && !empty($actividadesGeolocalizables)) {
        // Devolver las actividades geolocalizables en formato JSON
        header('Content-Type: application/json');
        echo json_encode($actividadesGeolocalizables);
    } else {
        // No se encontraron actividades geolocalizables
        header('Content-Type: application/json');
        echo json_encode(array('message' => 'No se encontraron actividades geolocalizables para el usuario.'));
    }
} else {
    // El idUsuario no está presente en la sesión
    header('Content-Type: application/json');
    echo json_encode(array('message' => 'No se encontró el idUsuario en la sesión.'));
}

<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos

require_once '../../bd/bd.php';

// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el idUsuario está presente en la sesión
if (isset($_SESSION['idUsuario'])) {
    // Obtener el idUsuario de la sesión
    $idUsuario = $_SESSION['idUsuario'];

    // Crear una instancia del objeto BD para interactuar con la base de datos
    $bd = new BD();

    // Obtener las actividades recomendadas para el usuario basadas en sus preferencias
    $actividadesRecomendadas = $bd->recomendarActividadesPersonalizadas($idUsuario);

    // Verificar si se obtuvieron actividades recomendadas
    if ($actividadesRecomendadas !== false && !empty($actividadesRecomendadas)) {
        // Devolver las actividades recomendadas en formato JSON
        header('Content-Type: application/json');
        //echo var_dump($actividadesRecomendadas);
        echo json_encode($actividadesRecomendadas);
    } else {
        // No se encontraron actividades recomendadas
        header('Content-Type: application/json');
        echo json_encode(array('message' => 'No se encontraron actividades recomendadas para el usuario.'));
    }
} else {
    // El idUsuario no está presente en la sesión
    header('Content-Type: application/json');
    echo json_encode(array('message' => 'No se encontró el idUsuario en la sesión.'));
}

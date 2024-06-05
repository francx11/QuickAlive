<?php
// Obtener las variables de entorno
$api_key = getenv('API_KEY');

// Verificar que la clave API se ha obtenido correctamente
if (!$api_key) {
    die('API_KEY no está configurada.');
}

// Obtener los parámetros de la solicitud
$latitude = isset($_GET['latitude']) ? $_GET['latitude'] : '';
$longitude = isset($_GET['longitude']) ? $_GET['longitude'] : '';
$radius = isset($_GET['radius']) ? $_GET['radius'] : 50;

// Verificar que las coordenadas se han recibido correctamente
if (empty($latitude) || empty($longitude)) {
    die('Las coordenadas de latitud y longitud son necesarias.');
}

// Construir el geoPoint combinando latitud y longitud
$geoPoint = $latitude . ',' . $longitude;

// Construir la URL de la API de TicketMaster
$url = 'https://app.ticketmaster.com/discovery/v2/events.json?apikey=' . $api_key . '&geoPoint=' . $geoPoint . '&radius=' . $radius;

// Hacer la solicitud a la API de TicketMaster
$response = @file_get_contents($url);

// Manejar errores de la solicitud
if ($response === FALSE) {
    $error = error_get_last();
    echo 'Error al hacer la solicitud: ' . $error['message'];
    exit;
}

// Devolver los resultados como JSON
header('Content-Type: application/json');
echo $response;

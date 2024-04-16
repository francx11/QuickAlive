<?php
// Obtener las variables de entorno y devolverlas como JSON
$client_id = getenv('API_KEY');
$client_secret = getenv('CLIENT_SECRET');

// Crear un array con las variables
$variables = array(
    'client_id' => $client_id,
    'client_secret' => $client_secret,
);

// Devolver las variables como JSON
echo json_encode($variables);

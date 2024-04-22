<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';

// Verificar si se ha recibido una solicitud POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el ID del usuario y las preferencias enviadas desde la solicitud POST
    $idUsuario = $_POST["idUsuario"];
    $preferencias = $_POST["preferencias"];

    // Crear una instancia de la clase BD para interactuar con la base de datos
    $bd = new BD();

    // Iterar sobre las preferencias seleccionadas y agregarlas a la base de datos
    foreach ($preferencias as $preferencia) {
        $nombreTipoPreferencia = $preferencia["nombreTipoPreferencia"];
        $idTipoPreferencia = $preferencia["idTipoPreferencia"];

        if ($bd->insertarPreferenciaPersonal($idUsuario, $nombreTipoPreferencia, $idTipoPreferencia)) {
            // Después de guardar las preferencias exitosamente
            //header("Location: ../pantallaInicial.php");
            echo 'Preferencia insertada correctamente';
        } else {
            // Mostrar un mensaje de error si no se pudieron insertar las preferencias
            http_response_code(500); // Internal Server Error
            echo "Error: No se pudieron insertar todas las preferencias";
            exit(); // Detener la ejecución del script
        }
    }
    exit();
} else {
    // Responder con un mensaje de error si no se recibió una solicitud POST válida
    http_response_code(400);
    echo "Error: Solicitud no válida";
}

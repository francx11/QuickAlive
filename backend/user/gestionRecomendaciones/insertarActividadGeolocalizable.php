<?php
require_once '../../bd/bd.php';

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombreActividad = $_POST['nombreActividad'];
        $descripcion = $_POST['descripcion'];
        $duracion = $_POST['duracion'];
        $urlImagen = $_POST['urlImagen'];
        $fechaLimite = $_POST['fechaLimite'];
        $idApi = $_POST['idApi'];

        $bd = new BD();
        $idActividad = $bd->insertarActividadGeolocalizable($nombreActividad, $descripcion, $duracion, $urlImagen, $idApi, $fechaLimite);

        if ($idActividad) {
            echo json_encode(['status' => 'success', 'idActividad' => $idActividad]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se pudo insertar la actividad']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Solicitud no válida']);
    }
}

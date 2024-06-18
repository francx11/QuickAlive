<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {
    $bd = new BD();
    $idUsuario = $_SESSION['idUsuario'];

    // Verificar si se ha enviado el formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verificar si todos los datos necesarios están presentes
        if (isset($_POST["idActividad"])) {
            // Obtener los datos del formulario
            $idActividad = $_POST["idActividad"];

            // Marcar la actividad como completada
            $resultado = $bd->completarActividad($idUsuario, $idActividad);

            // Verificar si se completó la actividad con éxito
            if ($resultado) {
                header("Location: renderListaActividades.php");
            } else {
                echo "<p>Ocurrió un error al marcar la actividad como completada.</p>";
            }
        } else {
            echo "<p>No se proporcionó el ID de la actividad.</p>";
        }
    } else {
        echo "<p>No se recibió una solicitud POST.</p>";
    }
}

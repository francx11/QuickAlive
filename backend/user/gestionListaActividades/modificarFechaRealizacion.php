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
        if (isset($_POST["idActividad"]) && isset($_POST["nuevaFecha"])) {
            // Obtener los datos del formulario
            $idActividad = $_POST["idActividad"];
            $nuevaFecha = $_POST["nuevaFecha"];

            // Realizar las comprobaciones adicionales necesarias, por ejemplo, verificar si la fecha es válida

            // Modificar la fecha de realización de la actividad
            $resultado = $bd->modificarFechaRealizacion($idUsuario, $idActividad, $nuevaFecha);

            // Verificar si la modificación fue exitosa
            if ($resultado) {

                header("Location: renderListaActividades.php");
            } else {
                echo "<p>Ocurrió un error al modificar la fecha de realización.</p>";
            }
        } else {
            echo "<p>No se proporcionaron todos los datos necesarios.</p>";
        }
    } else {
        echo "<p>No se recibió una solicitud POST.</p>";
    }
}

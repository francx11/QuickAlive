<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {
    // Verificar si se ha recibido una solicitud POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener el ID del usuario y el estado de la actividad enviados por POST
        $idUsuario = $_SESSION['idUsuario'];
        $idActividad = $_POST["idActividad"];
        $estado = $_POST["estado"];

        // Crear una instancia de la clase BD para interactuar con la base de datos
        $bd = new BD();

        // Verificar si el estado es "aceptada"
        if ($estado === "aceptada") {

            // Actualizar los intereses del usuario
            $categoriasActividad = $bd->getCategoriasActividad($idActividad);
            $preferenciasUsuario = $bd->getPreferenciasUsuario($idUsuario);

            if (!empty($preferenciasUsuario)) {
                $bd->actualizarPuntosInteres($categoriasActividad, $preferenciasUsuario, $estado);
            }

            // Descompletar la actividad
            $resultado = $bd->descompletarActividad($idUsuario, $idActividad);


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


            // Verificar si la descompletación fue exitosa
            if ($resultado) {
                // Redirigir a la página de historial de actividades
                header("Location: renderHistorialActividades.php");
                exit(); // Finalizar la ejecución del script
            } else {
                // Si ocurrió un error al descompletar la actividad, mostrar un mensaje de error
                echo "Error: No se pudo descompletar la actividad.";
            }
        } else {
            // Si el estado no es "aceptada", mostrar un mensaje de error
            echo "Error: El estado de la actividad no es válido.";
        }
    } else {
        // Responder con un mensaje de error si no se recibió una solicitud POST válida
        http_response_code(400);
        echo "Error: Solicitud no válida";
    }
} else {
    // Si el usuario no está logueado, redirigirlo al inicio de sesión u otra página de autenticación
    // header("Location: login.php");
    exit();
}

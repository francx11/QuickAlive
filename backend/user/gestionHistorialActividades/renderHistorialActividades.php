<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/user/templates/gestionHistorialActividades');
$twig = new \Twig\Environment($loader);

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {
    $bd = new BD();
    $idUsuario = $_SESSION['idUsuario'];

    $listaActividades = array();
    $historialActividades = $bd->obtenerHistorialActividades($idUsuario);

    foreach ($historialActividades as $actividadRealizada) {
        // Obtener toda la información de la actividad utilizando la función getActividad
        $actividadCompleta = $bd->getActividad($actividadRealizada['idActividad']);

        if ($actividadCompleta) {
            // Crear un array asociativo con los datos de la actividad
            $actividadData = [
                'idActividad' => $actividadRealizada['idActividad'],
                'nombreActividad' => $actividadCompleta->getNombreActividad(), // Ejemplo de cómo obtener el nombre de la actividad
                'descripcion' => $actividadCompleta->getDescripcion(), // Ejemplo de cómo obtener la descripción de la actividad
                'duracion' => $actividadCompleta->getDuracion(), // Ejemplo de cómo obtener la duración de la actividad
                'fechaRealizacion' => $actividadRealizada['fechaRealizacion'],
                'completada' => $actividadRealizada['completada']
            ];

            // Agregar el array asociativo a la lista de actividades
            $listaActividades[] = $actividadData;
        }
    }

    echo $twig->render('historialActividades.html', ['listaActividades' => $listaActividades, 'idUsuario' => $idUsuario]);
}

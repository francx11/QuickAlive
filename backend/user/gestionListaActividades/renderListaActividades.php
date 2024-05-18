<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/user/templates/gestionListaActividades');
$twig = new \Twig\Environment($loader);

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {
    $bd = new BD();
    $idUsuario = $_SESSION['idUsuario'];

    $listaActividades = array();
    $actividadesPendientes = $bd->obtenerActividadesArealizar($idUsuario);

    foreach ($actividadesPendientes as $actividadPendiente) {
        // Obtener toda la información de la actividad utilizando la función getActividad
        $actividadCompleta = $bd->getActividad($actividadPendiente['idActividad']);

        if ($actividadCompleta) {
            // Crear un array asociativo con los datos de la actividad
            $actividadData = [
                'idActividad' => $actividadPendiente['idActividad'],
                'nombreActividad' => $actividadCompleta->getNombreActividad(), // Ejemplo de cómo obtener el nombre de la actividad
                'descripcion' => $actividadCompleta->getDescripcion(), // Ejemplo de cómo obtener la descripción de la actividad
                'duracion' => $actividadCompleta->getDuracion(), // Ejemplo de cómo obtener la duración de la actividad
                'tipoActividad' => $actividadCompleta->getTipoActividad(),
                'fechaRealizacion' => $actividadPendiente['fechaRealizacion'],
                'completada' => $actividadPendiente['completada']
            ];

            // Agregar el array asociativo a la lista de actividades
            $listaActividades[] = $actividadData;
        }
    }

    // Renderizar el HTML con la lista de actividades
    echo $twig->render('listaActividades.html', ['listaActividades' => $listaActividades, 'idUsuario' => $idUsuario]);
}

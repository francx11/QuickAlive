<?php
// Verificar si la solicitud es AJAX y si el usuario está autenticado como administrador (root)
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    session_start();
    require_once "../../bd/bd.php";
    require "../../../vendor/autoload.php";

    $loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionActividades');
    $twig = new \Twig\Environment($loader);

    // Verificar si el usuario está autenticado como administrador (root)
    $registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
    $logueado = $_SESSION['loggedin'];

    // Verificar si se proporciona un ID de imagen válido en la solicitud AJAX
    if ($registradoRoot && isset($_POST['imagenId'])) {
        $numImagen = $_POST['imagenId'];
        // Crear una instancia de la clase BD y establecer la conexión
        $bd = new BD();

        // Intentar eliminar la imagen de la galería
        if ($bd->eliminarFotoGaleria($numImagen)) {
            // Enviar una respuesta JSON con el estado de éxito

            //$imagenesActualizadas = $bd->getGaleriaActividad($idActividad);
            //$html = $twig->render('modificarActividad.html', ['imagenes' => $imagenesActualizadas]);
            echo json_encode(array('success' => true));
        } else {
            // Enviar una respuesta JSON con el estado de error si la eliminación falla
            echo json_encode(array('success' => false, 'message' => 'Error en la eliminación de la imagen'));
        }

    } else {
        // Enviar una respuesta JSON con el estado de error si el usuario no tiene permisos de administrador o no se proporciona un ID de imagen válido
        echo json_encode(array('success' => false, 'message' => 'Acceso no autorizado o ID de imagen no válido'));
    }
} else {
    // Enviar una respuesta JSON con el estado de error si la solicitud no es AJAX
    echo json_encode(array('success' => false, 'message' => 'Acceso no autorizado'));
}

<?php
require_once "../../bd/bd.php";
require_once "../../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionPreferencias');
$twig = new \Twig\Environment($loader);

$bd = new BD();

// Verificar la sesión del usuario
if (isset($_SESSION['loggedin'], $_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root') {
    $bd->iniciarConexion();
    $logueado = $_SESSION['loggedin'];

    $tipoOperacion = isset($_GET['tipoOperacion']) ? $_GET['tipoOperacion'] : "añadir";
    // Obtener el ID de la preferencia padre
    $idTipoPreferencia = isset($_GET['id']) ? $_GET['id'] : -1;

    // Obtener el tipo de prefencia al que pertenece
    $tipoPreferencia = isset($_GET['tipoPreferencia']) ? $_GET['tipoPreferencia'] : "Deportivas";

    // Obtener las preferencias hijas de la preferencia padre
    $preferencias = $bd->obtenerPreferencias($idTipoPreferencia, $tipoPreferencia);

    //echo var_dump($subPreferencias);

    // Verificar si se envió una solicitud POST para eliminar una preferencia hija
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if ($tipoOperacion == "borrar" && isset($_POST['idPreferencia'])) {
            $idPreferencia = $_POST['idPreferencia'];

            // Llamar a la función para eliminar la preferencia hija
            if ($bd->eliminarPreferencia($idPreferencia, $tipoPreferencia)) {
                // Si la eliminación fue exitosa, redirigir a la misma página para actualizar la lista
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $idTipoPreferencia);
                exit();
            } else {
                // Si la eliminación falló, mostrar un mensaje de error en la página
                echo "Error al eliminar la preferencia hija";
            }

        } else if ($tipoOperacion == "añadir") {
            echo var_dump($_POST['nombrePreferencia']);

            if (isset($_POST['nombrePreferencia'])) {
                $nombrePreferencia = $_POST['nombrePreferencia'];
            } else {
                $nombrePreferencia = "";
            }

            // Llamar a la función para añadir una preferencia hija
            if ($bd->insertarPreferencia($idTipoPreferencia, $tipoPreferencia, $nombrePreferencia)) {
                header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $idTipoPreferencia);
            } else {
                echo "Error en la adición de una subPreferencia";
            }
        }

    }

    // Renderizar el template con la lista de preferencias hijas
    echo $twig->render('modificarTipoPreferencia.html', ['preferencias' => $preferencias, 'idTipoPreferencia' => $idTipoPreferencia, 'logueado' => $logueado]);

} else {
    // Si el usuario no tiene permisos suficientes, mostrar un mensaje de error en la página
    echo "No tienes permiso para realizar esta acción";
}

$bd->cerrarConexion();

<?php
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionActividades');
$twig = new \Twig\Environment($loader);

$bd = new BD();

$idActividad = isset($_GET['id']) ? $_GET['id'] : -1;

$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
$logueado = $_SESSION['loggedin'];

if ($registradoRoot) {
    //echo 'Bien';
    $bd->iniciarConexion();

    $actividadOriginal = $bd->getActividad($idActividad);
    //echo var_dump($actividadOriginal);

    $galeriaActual = $bd->getGaleriaActividad($idActividad);
    //echo var_dump($galeriaActual);
    //echo var_dump($_SERVER["REQUEST_METHOD"] == "POST");

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo 'Aqui entro';

        $datosBasicosCorrectos = isset($_POST['nombreActividad'], $_POST['descripcion'], $_POST['tipoActividad'], $_POST['duracion']);
        echo $datosBasicosCorrectos;

        if ($datosBasicosCorrectos) {

            $nombreActividad = $_POST['nombreActividad'];
            $descripcion = $_POST['descripcion'];
            $tipoActividad = $_POST['tipoActividad'];
            $duracion = $_POST['duracion'];

            // Verificar qué campos se están modificando y establecer valores predeterminados si no se están modificando
            $nombreActividad = ($nombreActividad != '') ? $nombreActividad : $actividadOriginal->getNombreActividad();
            $descripcion = ($descripcion != '') ? $descripcion : $actividadOriginal->getDescripcion();
            $tipoActividad = ($tipoActividad != '') ? $tipoActividad : $actividadOriginal->getTipoActividad();
            $duracion = ($duracion != '') ? $duracion : $actividadOriginal->getDuracion();

            if ($bd->modificarActividad($idActividad, $nombreActividad, $descripcion, $tipoActividad, $duracion)) {
                echo 'Datos básicos modificados con éxito';
            } else {
                echo 'Error en modificación de datos básicos';
            }

            header("Location: gestionActividades.php");
        } else {
            echo 'Error en POST de algún dato básico';
        }

        if ($_FILES['imagenes']) {
            $imagenesPOST = $_FILES['imagenes']['tmp_name'];

            if ($idActividad) {
                //echo $idActividad;

                if (!empty($_FILES['imagenes']['name'][0])) {
                    $numImagenes = count($_FILES['imagenes']['name']);
                    //echo $numImagenes;
                    $directorioDestino = "imgs/";

                    for ($i = 0; $i < $numImagenes; $i++) {
                        $nombreImagen = $_FILES['imagenes']['name'][$i];
                        $url = $directorioDestino . $nombreImagen;

                        if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $url)) {
                            echo "La imagen $nombreImagen se ha subido correctamente. Ruta: $url<br>";

                            if ($bd->agregarFotoGaleria($idActividad, $url)) {
                                echo 'Imagen insertada con éxito';
                            } else {
                                echo 'Fallo al insertar la imagen';
                            }
                        }

                    }

                } else {
                    echo "Error en al subir la imagen";
                }

                //header("Location: gestionActividades.php");
                header("Location: gestionActividades.php");
            } else {
                echo "ID de actividad erróneo.";
            }
        }

    }

    echo $twig->render('modificarActividad.html', ['idActividad' => $idActividad, 'imagenes' => $galeriaActual, 'logueado' => $logueado]);

}

<?php
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionActividades');
$twig = new \Twig\Environment($loader);

$bd = new BD();

$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';

if ($registradoRoot) {
    $bd->iniciarConexion();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Verificar si se han enviado los datos del formulario
        if (isset($_POST['nombre'], $_POST['descripcion'], $_POST['tipo'], $_POST['duracion'], $_FILES['imagenes'])) {
            $nombreActividad = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $tipoActividad = $_POST['tipo'];
            $duracion = $_POST['duracion'];
            $fotos = $_FILES['imagenes']['tmp_name'];

            // Insertar la actividad simple
            $idActividad = $bd->insertarActividadSimple($nombreActividad, $descripcion, $tipoActividad, $duracion);
            echo "El id de la actividad es:  $idActividad";

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

                echo "Actividad creada exitosamente.";
            } else {
                echo "Error al crear la actividad.";
            }
        } else {
            echo "Todos los campos son obligatorios.";
        }
    }

    echo $twig->render('altaActividad.html', []);
}
$bd->cerrarConexion();

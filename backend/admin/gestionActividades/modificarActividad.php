<?php
// Incluye el archivo de la base de datos y la librería Twig
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

// Inicia la sesión
session_start();

// Crea una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Verifica si el usuario está autenticado como root
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';

// Si el usuario es root, procede con la modificación de la actividad
if ($registradoRoot) {




    // Obtiene el ID de la actividad de la solicitud POST, o establece -1 si no se proporciona
    $idActividad = isset($_POST['idActividad']) ? $_POST['idActividad'] : -1;

    // Obtiene los datos de la actividad original
    $actividadOriginal = $bd->getActividad($idActividad);

    // Si se ha enviado una solicitud POST, procesa la modificación de la actividad
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verifica si se han proporcionado los datos básicos de la actividad en el formulario
        $datosBasicosCorrectos = isset($_POST['nombre'], $_POST['descripcion'], $_POST['duracion']);

        // Si los datos básicos son correctos, procede con la modificación
        if ($datosBasicosCorrectos) {
            // Obtiene los datos básicos de la actividad del formulario
            $nombreActividad = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $duracion = $_POST['duracion'];

            // Verifica si se están modificando los campos y establece valores predeterminados si es necesario
            $nombreActividad = ($nombreActividad != '') ? $nombreActividad : $actividadOriginal->getNombreActividad();
            $descripcion = ($descripcion != '') ? $descripcion : $actividadOriginal->getDescripcion();
            $duracion = ($duracion != '') ? $duracion : $actividadOriginal->getDuracion();

            // Intenta modificar la actividad en la base de datos
            if ($bd->modificarActividad($idActividad, $nombreActividad, $descripcion, $duracion)) {
                echo 'Datos básicos modificados con éxito';
            } else {
                echo 'Error en modificación de datos básicos';
            }
        } else {
            echo 'Error en POST de algún dato básico';
        }

        if (isset($_POST['preferencias'])) {
            // Decodificar las preferencias desde el JSON
            $categoriasJSON = $_POST['preferencias'];
            $categorias = json_decode($categoriasJSON, true);

            if ($bd->modificarTipoPreferencias($idActividad, $categorias)) {
                echo 'Modificadas correctamente las categorías';
            }
        }

        // Procesa la carga de nuevas imágenes para la galería de la actividad, si se han proporcionado
        if (isset($_FILES['imagenes'])) {
            // Obtiene las imágenes subidas del formulario
            $imagenesPOST = $_FILES['imagenes']['tmp_name'];

            // Verifica si se ha proporcionado un ID de actividad válido
            if ($idActividad) {
                // Itera sobre las imágenes subidas y las agrega a la galería de la actividad
                foreach ($_FILES['imagenes']['name'] as $key => $nombreImagen) {

                    // Define el directorio de destino para las imágenes
                    $directorioDestino = '../../../imgs/';



                    // Construye la URL completa para la imagen
                    $url = $directorioDestino . $nombreImagen;

                    // Mueve la imagen al directorio de destino
                    if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$key], $url)) {
                        echo "La imagen $nombreImagen se ha subido correctamente. Ruta: $url<br>";

                        // Agrega la imagen a la galería de la actividad en la base de datos
                        if ($bd->agregarFotoGaleria($idActividad, $url) != -1) {
                            echo 'Imagen insertada con éxito';
                        } else {
                            echo 'Fallo al insertar la imagen';
                        }
                    }
                }
            } else {
                echo "ID de actividad erróneo.";
            }
        }
    }
}

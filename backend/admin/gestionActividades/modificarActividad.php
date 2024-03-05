<?php
// Incluye el archivo de la base de datos y la librería Twig
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

// Inicia la sesión
session_start();

// Configura el cargador y el entorno de Twig para las plantillas
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionActividades');
$twig = new \Twig\Environment($loader);

// Crea una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Obtiene el ID de la actividad de la solicitud GET, o establece -1 si no se proporciona
$idActividad = isset($_GET['id']) ? $_GET['id'] : -1;

// Verifica si el usuario está autenticado como root
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';

// Si el usuario es root, procede con la modificación de la actividad
if ($registradoRoot) {
    // Inicia la conexión con la base de datos
    $bd->iniciarConexion();

    // Obtiene los datos de la actividad original
    $actividadOriginal = $bd->getActividad($idActividad);

    // Obtiene la galería de imágenes actual de la actividad
    $galeriaActual = $bd->getGaleriaActividad($idActividad);

    // Si se ha enviado una solicitud POST, procesa la modificación de la actividad
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Verifica si se han proporcionado los datos básicos de la actividad en el formulario
        $datosBasicosCorrectos = isset($_POST['nombreActividad'], $_POST['descripcion'], $_POST['tipoActividad'], $_POST['duracion']);

        // Si los datos básicos son correctos, procede con la modificación
        if ($datosBasicosCorrectos) {
            // Obtiene los datos básicos de la actividad del formulario
            $nombreActividad = $_POST['nombreActividad'];
            $descripcion = $_POST['descripcion'];
            $tipoActividad = $_POST['tipoActividad'];
            $duracion = $_POST['duracion'];

            // Verifica si se están modificando los campos y establece valores predeterminados si es necesario
            $nombreActividad = ($nombreActividad != '') ? $nombreActividad : $actividadOriginal->getNombreActividad();
            $descripcion = ($descripcion != '') ? $descripcion : $actividadOriginal->getDescripcion();
            $tipoActividad = ($tipoActividad != '') ? $tipoActividad : $actividadOriginal->getTipoActividad();
            $duracion = ($duracion != '') ? $duracion : $actividadOriginal->getDuracion();

            // Intenta modificar la actividad en la base de datos
            if ($bd->modificarActividad($idActividad, $nombreActividad, $descripcion, $tipoActividad, $duracion)) {
                echo 'Datos básicos modificados con éxito';
            } else {
                echo 'Error en modificación de datos básicos';
            }

            // Redirige de vuelta a la página de gestión de actividades
            header("Location: gestionActividades.php");
        } else {
            echo 'Error en POST de algún dato básico';
        }

        // Procesa la carga de nuevas imágenes para la galería de la actividad, si se han proporcionado
        if ($_FILES['imagenes']) {
            // Obtiene las imágenes subidas del formulario
            $imagenesPOST = $_FILES['imagenes']['tmp_name'];

            // Verifica si se ha proporcionado un ID de actividad válido
            if ($idActividad) {
                // Itera sobre las imágenes subidas y las agrega a la galería de la actividad
                foreach ($_FILES['imagenes']['name'] as $key => $nombreImagen) {
                    // Define el directorio de destino para las imágenes
                    $directorioDestino = "imgs/";

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

                // Redirige de vuelta a la página de gestión de actividades
                header("Location: gestionActividades.php");
            } else {
                echo "ID de actividad erróneo.";
            }
        }
    }

    // Renderiza la plantilla Twig 'modificarActividad.html', pasando el ID de la actividad y las imágenes de la galería actual
    echo $twig->render('modificarActividad.html', ['idActividad' => $idActividad, 'imagenes' => $galeriaActual]);
}

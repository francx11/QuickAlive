<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../../vendor/autoload.php";

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionActividades');
$twig = new \Twig\Environment($loader);

// Crear una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Verificar si el usuario está registrado como 'root' y está logueado
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
// Obtener el estado de inicio de sesión del usuario
$logueado = $_SESSION['loggedin'];

// Si el usuario está registrado como 'root'
if ($registradoRoot) {
    // Si se ha enviado una solicitud POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Verificar si se han enviado los datos del formulario
        if (isset($_POST['nombre'], $_POST['descripcion'], $_POST['tipo'], $_POST['subtipo'], $_POST['duracion'], $_FILES['imagenes'])) {
            // Obtener los datos del formulario
            $nombreActividad = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $tipoActividad = $_POST['tipo'];
            $subTipoActividad = $_POST['subtipo'];
            $duracion = $_POST['duracion'];
            $fotos = $_FILES['imagenes']['tmp_name'];

            // Insertar la actividad simple en la base de datos
            $idActividad = $bd->insertarActividadSimple($nombreActividad, $descripcion, $tipoActividad, $subTipoActividad, $duracion);

            // Si se ha insertado correctamente
            if ($idActividad) {
                // Subir las imágenes asociadas a la actividad al servidor y registrar las rutas en la base de datos
                if (!empty($_FILES['imagenes']['name'][0])) {
                    $numImagenes = count($_FILES['imagenes']['name']);
                    $directorioDestino = "imgs/";

                    for ($i = 0; $i < $numImagenes; $i++) {
                        $nombreImagen = $_FILES['imagenes']['name'][$i];
                        $url = $directorioDestino . $nombreImagen;

                        if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$i], $url)) {
                            if ($bd->agregarFotoGaleria($idActividad, $url) != -1) {
                                echo 'Imagen insertada con éxito';
                            } else {
                                echo 'Fallo al insertar la imagen';
                            }
                        }
                    }
                }

                //echo "Actividad creada exitosamente.";
                header("Location: gestionActividades.php");
            } else {
                echo "Error al crear la actividad.";
            }

        } else {
            echo "Todos los campos son obligatorios.";
        }
    }

    // Renderizar la plantilla 'altaActividad.html' utilizando Twig
    echo $twig->render('altaActividad.html', ['logueado' => $logueado]);
}

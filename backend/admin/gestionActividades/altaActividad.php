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
        echo var_dump($_FILES['imagenes']);

        // Verificar si se han enviado los datos del formulario
        if (isset($_POST['nombre'], $_POST['descripcion'], $_POST['duracion'], $_POST['preferencias'], $_FILES['imagenes'])) {

            // Obtener los datos del formulario
            $nombreActividad = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $duracion = $_POST['duracion'];
            $fotos = $_FILES['imagenes']['tmp_name'];
            // Decodificar las preferencias desde el JSON
            $categoriasJSON = $_POST['preferencias'];
            $categorias = json_decode($categoriasJSON, true);


            //echo var_dump($categorias);

            // Insertar la actividad simple en la base de datos
            $idActividad = $bd->insertarActividadSimple($nombreActividad, $descripcion, $duracion);

            if ($idActividad) {
                $insercionCategorias = $bd->insertarActividadConCategorias($idActividad, $categorias);
                // Subir las imágenes asociadas a la actividad al servidor y registrar las rutas en la base de datos
                if (!empty($_FILES['imagenes']['name'][0])) {
                    // Itera sobre las imágenes subidas y las agrega a la galería de la actividad
                    foreach ($_FILES['imagenes']['name'] as $key => $nombreImagen) {

                        // Define el directorio de destino para las imágenes
                        $directorioDestino = '../../../imgs/' . $nombreImagen;

                        // Construye la URL completa para la imagen
                        $url = "/quickalive/imgs/" . $nombreImagen;

                        // Mueve la imagen al directorio de destino
                        if (move_uploaded_file($_FILES['imagenes']['tmp_name'][$key], $directorioDestino)) {
                            echo "La imagen $nombreImagen se ha subido correctamente. Ruta: $url<br>";

                            // Agrega la imagen a la galería de la actividad en la base de datos
                            if ($bd->agregarFotoGaleria($idActividad, $url) != -1) {
                                echo 'Imagen insertada con éxito';
                            } else {
                                echo 'Fallo al insertar la imagen';
                            }
                        }
                    }
                    //echo "Actividad creada exitosamente.";
                    //header("Location: renderAltaActividad.php");
                } else {
                    echo "No se ha seleccionado ninguna imagen";
                }
            } else {
                echo 'Id de actividad erróneo';
            }
        }
    }
}

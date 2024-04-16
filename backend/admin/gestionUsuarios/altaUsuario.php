<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../../vendor/autoload.php";

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();

// Crear un cargador de plantillas Twig para cargar plantillas desde un directorio específico
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionUsuarios');
// Crear una instancia de Twig para procesar las plantillas
$twig = new \Twig\Environment($loader);

// Crear una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Verificar si el usuario está registrado como 'root' y está logueado
$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
// Obtener el estado de inicio de sesión del usuario
$logueado = $_SESSION['loggedin'];

// Si el usuario está registrado como 'root'
if ($registradoRoot) {

    // Si se ha enviado una solicitud POST desde el formulario
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Obtener los datos del formulario
        $nickName = $_POST['nickName'];
        $telefono = $_POST['telefono'];
        $correo = $_POST['correo'];
        $password = $_POST['password'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $edad = $_POST['edad'];
        $rol = $_POST['rol'];

        // Insertar el nuevo usuario en la base de datos
        if (!$bd->insertarUsuario($nickName, $telefono, $correo, $password, $nombre, $apellidos, $edad, $rol)) {
            echo 'Error en la inserción del usuario';
        }

        // Redirigir al usuario de vuelta a la misma página para evitar envíos duplicados del formulario
        header("Location: gestionUsuarios.php");
        exit();
    }

    // Renderizar la plantilla 'altaUsuario.html' usando Twig, pasando el estado de inicio de sesión
    echo $twig->render('altaUsuario.html', ['logueado' => $logueado]);
}

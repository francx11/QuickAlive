<?php
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionUsuarios');
$twig = new \Twig\Environment($loader);

$bd = new BD();

$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';

if ($registradoRoot) {
    $bd->iniciarConexion();
    // Funcionalidad para la inserción de usuarios

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Obtener los datos del formulario
        $nickName = $_POST['nickName'];
        $telefono = $_POST['telefono'];

        // TODO: Verificar correo
        $correo = $_POST['correo'];
        $password = $_POST['password'];
        $nombre = $_POST['nombre'];
        $apellidos = $_POST['apellidos'];
        $edad = $_POST['edad'];
        $rol = $_POST['rol'];

        if (!$bd->insertarUsuario($nickName, $telefono, $correo, $password, $nombre,
            $apellidos, $edad, $rol)) {
            echo 'Error en la inserción del usuario';

        }

        // Redirigir a la misma página
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();

    }
    echo $twig->render('altaUsuario.html', []);

}

$bd->cerrarConexion();

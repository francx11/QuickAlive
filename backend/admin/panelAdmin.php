<?php
require_once "../bd/bd.php";
require "../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../frontend/admin/templates');
$twig = new \Twig\Environment($loader);

$bd = new BD();

$bd->iniciarConexion();
// Temporalmente
$_SESSION['nickName'] = 'root';
$_SESSION['rol'] = $bd->getRol($_SESSION['nickName']);

if ( /*_SESSION['loggedin']*/$_SESSION['rol'] == 'root') {
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

    }

}

echo $twig->render('panelAdmin.html', []);

$bd->cerrarConexion();

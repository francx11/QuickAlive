<?php
require_once "../../../vendor/autoload.php";
require_once '../../bd/bd.php';

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/common/templates');
$twig = new \Twig\Environment($loader);

$bd = new BD();

$bd->iniciarConexion();

$inicio = false;

// Obtener del formulario los datos del usuario

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nick = $_POST['nickName'];
    $pass = $_POST['password'];

    if ($bd->checkLogin($nick, $pass)) {
        session_start();

        $_SESSION['nickName'] = $nick; // guardo en la sesión el nick del usuario que se ha logueado
        $_SESSION['loggedin'] = true; // esta variable sirve para saber si el usuario está loggeado
        //$_SESSION['email'] = $bd->getEmail($nick);
        $_SESSION['password'] = $pass;
        $_SESSION['rol'] = $bd->getRol($nick);

        //echo "Usuario iniciando sesion";
        $inicio = true;
        header("Location: ../../../index.php");

    } else {
        //echo 'Nombre de usuario o contraseña incorrectos';
        $inicio = false;
    }
}

$bd->cerrarConexion();

echo $twig->render('login.html', ['inicio' => $inicio]);

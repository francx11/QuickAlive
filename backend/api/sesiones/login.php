<?php
require_once "../../../vendor/autoload.php";
require_once '../../bd/bd.php';

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/common/templates');
$twig = new \Twig\Environment($loader);

$bd = new BD();

$bd->iniciarConexion();

$inicio = 0;

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

        echo $_SESSION['rol'];

        //echo "Usuario iniciando sesion";
        $inicio = 1;

        if ($_SESSION['rol'] == 'root') {
            header("Location: ../../../backend/admin/panelAdmin.php");
        } else {
            header("Location: ../../../index.php");
        }

    } else {
        //echo 'Nombre de usuario o contraseña incorrectos';
        $inicio = 2;
    }
}

$bd->cerrarConexion();

echo $twig->render('login.html', ['inicio' => $inicio]);

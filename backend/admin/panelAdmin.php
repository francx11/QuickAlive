<?php
require_once "../bd/bd.php";
require "../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../frontend/admin/templates');
$twig = new \Twig\Environment($loader);

$bd = new BD();

$bd->iniciarConexion();

if ($_SESSION['loggedin'] && $_SESSION['rol'] == 'root') {
    // Buscar a un usuario

    if (isset($_POST['nickNameBuscado'])) {
        $nickName = $_POST['nickNameBuscado'];
    }

    $usuariosBuscados = $bd->buscarCoincidenciasUsuario($nickName);
    echo json_encode($usuariosBuscados);

}

$bd->cerrarConexion();

echo $twig->render('panelAdmin.html', []);

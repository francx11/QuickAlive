<?php
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionPreferencias');
$twig = new \Twig\Environment($loader);

$bd = new BD();

$registradoRoot = isset($_SESSION['loggedin']) && isset($_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root';
$logueado = $_SESSION['loggedin'];

if ($registradoRoot) {
    $bd->iniciarConexion();

    // Funcionalidad para la inserción de preferencias

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        // Obtener los datos del formulario
        $tipoPreferencia = $_POST['tipoPreferencia'];

        if (!$bd->insertarTipoDePreferencia($tipoPreferencia)) {
            echo 'Error en la inserción de la preferencia';
        }

        // Redirigir a la misma página
        header("Location: gestionPreferencias.php");
        exit();
    }

    echo $twig->render('altaTipoPreferencia.html', ['logueado' => $logueado]);
}

$bd->cerrarConexion();

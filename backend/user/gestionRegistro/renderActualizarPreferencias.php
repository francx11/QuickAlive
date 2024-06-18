<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/user/templates/gestionRegistro');
$twig = new \Twig\Environment($loader);

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {
    $bd = new BD();
    $idUsuario = $_SESSION['idUsuario'];

    //echo var_dump($idUsuario);
    $tiposPreferencias = $bd->getAllTipoPreferencias();

    $preferenciasAnteriores = $bd->getPreferenciasUsuario($idUsuario);

    //echo var_dump($idUsuario);
    echo $twig->render('actualizarPreferencias.html', ['tiposPreferencias' => $tiposPreferencias, 'preferenciasAnteriores' => $preferenciasAnteriores, 'idUsuario' => $idUsuario]);
}

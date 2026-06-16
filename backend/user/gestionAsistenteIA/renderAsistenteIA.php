<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/user/templates/gestionAsistenteIA');
$twig = new \Twig\Environment($loader);

session_start();

$logueado = $_SESSION['loggedin'];

if ($logueado) {
    $bd = new BD();
    $idUsuario = $_SESSION['idUsuario'];

    if ($bd->esUsuarioPremium($idUsuario)) {
        echo $twig->render('asistente.html', ['idUsuario' => $idUsuario]);
    } else {
        echo $twig->render('upsellPremium.html', []);
    }
}

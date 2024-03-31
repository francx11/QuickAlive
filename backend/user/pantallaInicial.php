<?php
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once "../bd/bd.php";
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require "../../vendor/autoload.php";

// Crear un cargador de Twig para cargar plantillas desde el directorio '../../frontend/admin/templates/'
$loader = new \Twig\Loader\FilesystemLoader('../../frontend/user/templates/');
// Crear una instancia de Twig Environment utilizando el cargador creado anteriormente
$twig = new \Twig\Environment($loader);

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();
$logueado = $_SESSION['loggedin'];

if ($logueado) {
    echo $twig->render('pantallaInicial.html', []);
}

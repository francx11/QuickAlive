<?php
// Inicia una sesión PHP para manejar variables de sesión
session_start();

// Carga la clase autoloader de Twig para cargar automáticamente las clases necesarias de Twig
require_once 'vendor/autoload.php';

// Configuración de Twig para cargar plantillas desde el directorio actual
$loader = new \Twig\Loader\FilesystemLoader('./');
$twig = new \Twig\Environment($loader);

// Variables para controlar el estado de la sesión y el rol del usuario
$logueado = false;
$rol = "";

// Verifica si las variables de sesión 'loggedin' y 'rol' están definidas y no son nulas
if (isset($_SESSION['loggedin']) && isset($_SESSION['rol'])) {
    // Asigna los valores de las variables de sesión a las variables locales
    $logueado = $_SESSION['loggedin'];
    $rol = $_SESSION['rol'];
}

// Carga el template 'index.html' utilizando Twig
$template = $twig->load('index.html');

// Renderiza el template 'index.html' pasando las variables 'logueado' y 'rol' al template
echo $template->render(['logueado' => $logueado, 'rol' => $rol]);

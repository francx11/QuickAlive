<?php

require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('./');
$twig = new \Twig\Environment($loader);

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo 'Script ejecutado'; // Mensaje de depuración

$template = $twig->load('index.html');
echo $template->render(['variable' => 'Bienvenido!']);

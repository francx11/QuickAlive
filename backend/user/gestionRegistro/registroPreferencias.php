<?php
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/user/templates/gestionRegistro');
$twig = new \Twig\Environment($loader);


// Crear una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Obtiene el ID del usuario de la solicitud GET, o establece -1 si no se proporciona
$idUsuario = isset($_GET['id']) ? $_GET['id'] : -1;

$tipoPreferencias = $bd->getAllTipoPreferencias();

echo $twig->render('registroPreferencias.html', ['tiposPreferencias' => $tipoPreferencias, 'idUsuario' => $idUsuario]);

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

$tipoPreferencias = $bd->getAllTipoPreferencias();

//echo var_dump($tipoPreferencias);

// Crear un nuevo array para almacenar los datos con el formato deseado
$tipoPreferenciasFormateado = [];

// Iterar sobre cada tipo de preferencia y obtener sus preferencias asociadas
foreach ($tipoPreferencias as $tipoPreferencia) {
    $idTipoPreferencia = $tipoPreferencia['idTipoPreferencia'];
    $nombreTipoPreferencia = $tipoPreferencia['tipoPreferencia'];

    // Obtener las preferencias asociadas a este tipo de preferencia
    $preferencias = $bd->obtenerPreferencias($idTipoPreferencia, $nombreTipoPreferencia);

    // Construir el array asociativo para este tipo de preferencia
    $tipoPreferenciaFormateado = [
        'idTipoPreferencia' => $idTipoPreferencia,
        'tipoPreferencia' => $nombreTipoPreferencia,
        'preferencias' => $preferencias,
    ];

    // Agregar el array asociativo al array principal
    $tipoPreferenciasFormateado[] = $tipoPreferenciaFormateado;

}

echo $twig->render('registroBasico.html', ['tiposPreferencias' => $tipoPreferenciasFormateado]);

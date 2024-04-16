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

// Si se ha enviado una solicitud POST desde el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    /*
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
     */

    // Obtener los datos del formulario
    $nickName = $_POST['nickName'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $edad = $_POST['edad'];
    $rol = $_POST['role'];

    //echo var_dump($rol);

    // Insertar el nuevo usuario en la base de datos
    if (!$bd->insertarUsuario($nickName, $telefono, $correo, $password, $nombre, $apellidos, $edad, $rol)) {
        echo 'Error en la inserción del usuario';
    }

    $usuario = $bd->getUsuario($nickName);

    $idUsuario = $usuario->getIdUsuario();

    // Redirigir al usuario de vuelta a la misma página para evitar envíos duplicados del formulario
    header("Location: registroPreferencias.php?id=" . $idUsuario);
    exit();
}

echo $twig->render('registroBasico.html', []);

<?php
require_once "../../bd/bd.php";
require "../../../vendor/autoload.php";

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/admin/templates/gestionUsuarios');
$twig = new \Twig\Environment($loader);

$bd = new BD();

// Obtener el ID de usuario de la URL o establecer un valor predeterminado de -1
$idUsuario = isset($_GET['id']) ? $_GET['id'] : -1;
//echo $idUsuario;

// Verificar la sesión del usuario y sus permisos
if (isset($_SESSION['loggedin'], $_SESSION['rol']) && $_SESSION['loggedin'] && $_SESSION['rol'] == 'root') {
    $logueado = $_SESSION['loggedin'];

    // Obtener los datos del usuario original
    $usuarioOriginal = $bd->getUsuarioPorId($idUsuario);

    // Verificar si se envió una solicitud POST para modificar el usuario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_POST['nickName'], $_POST['telefono'], $_POST['correo'], $_POST['password'], $_POST['nombre'], $_POST['apellidos'], $_POST['edad'], $_POST['rol'])) {
            // Obtener los datos del formulario
            echo 'Entra aqui tb';
            $nickName = $_POST['nickName'];
            $telefono = $_POST['telefono'];
            $correo = $_POST['correo'];
            $password = $_POST['password'];
            $nombre = $_POST['nombre'];
            $apellidos = $_POST['apellidos'];
            $edad = $_POST['edad'];
            $root = $_POST['rol'];

            // Verificar qué campos se están modificando y establecer valores predeterminados si no se están modificando
            $nickName = ($nickName != '') ? $nickName : $usuarioOriginal->getNickName();
            $telefono = ($telefono != '') ? $telefono : $usuarioOriginal->getTelefono();
            $correo = ($correo != '') ? $correo : $usuarioOriginal->getCorreo();
            $password = ($password != '') ? $password : $usuarioOriginal->getPassword();
            $nombre = ($nombre != '') ? $nombre : $usuarioOriginal->getNombre();
            $apellidos = ($apellidos != '') ? $apellidos : $usuarioOriginal->getApellidos();
            $edad = ($edad != '') ? $edad : $usuarioOriginal->getEdad();
            $rol = ($rol != '') ? $rol : $usuarioOriginal->getRol();

            // Llamar a la función para modificar el usuario
            if ($bd->modificarUsuario($idUsuario, $nickName, $telefono, $correo, $password, $nombre, $apellidos, $edad, $rol)) {
                // Si la modificación fue exitosa, redirigir a una página de éxito
                header("Location: gestionUsuarios.php");
                //exit();
            } else {
                // Si la modificación falló, mostrar un mensaje de error en la página
                echo "Error al modificar el usuario";
            }
        }
    }
    // Renderizar el formulario de modificación del usuario
    echo $twig->render('modificarUsuario.html', ['idUsuario' => $idUsuario, 'logueado' => $logueado]);
} else {
    // Si el usuario no tiene permisos suficientes, mostrar un mensaje de error en la página
    echo "No tienes permiso para realizar esta acción";
}

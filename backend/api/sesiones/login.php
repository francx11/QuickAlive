<?php
// Cargar el autoloader de Composer para cargar las clases automáticamente
require_once "../../../vendor/autoload.php";
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/common/templates');
$twig = new \Twig\Environment($loader);

// Crear una instancia de la clase BD para interactuar con la base de datos
$bd = new BD();

// Iniciar una conexión a la base de datos
$bd->iniciarConexion();

$inicio = 0; // Variable para controlar el estado del inicio de sesión

// Verificar si se ha enviado el formulario de inicio de sesión mediante el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener el nombre de usuario y la contraseña del formulario
    $nick = $_POST['nickName'];
    $pass = $_POST['password'];

    // Verificar las credenciales del usuario en la base de datos
    if ($bd->checkLogin($nick, $pass)) {
        // Iniciar una sesión PHP
        session_start();

        // Guardar en la sesión la información del usuario que se ha logueado
        $_SESSION['nickName'] = $nick; // Nick del usuario
        $_SESSION['loggedin'] = true; // Variable para indicar que el usuario está logueado
        $_SESSION['password'] = $pass; // Contraseña del usuario (nota: guardar contraseñas en sesión no es recomendado por motivos de seguridad)
        $_SESSION['rol'] = $bd->getRol($nick); // Rol del usuario

        // Redirigir al usuario según su rol
        if ($_SESSION['rol'] == 'root') {
            // Si el usuario es administrador, redirigir al panel de administración
            header("Location: ../../../backend/admin/panelAdmin.php");
        } else {
            // Si el usuario no es administrador, redirigir a la página principal
            header("Location: ../../../index.php");
        }
    } else {
        // Las credenciales son incorrectas
        $inicio = 2;
    }
}

// Cerrar la conexión a la base de datos
$bd->cerrarConexion();

// Renderizar la plantilla 'login.html' utilizando Twig, pasando el estado del inicio de sesión
echo $twig->render('login.html', ['inicio' => $inicio]);

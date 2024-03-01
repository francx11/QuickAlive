<?php
// Incluir el archivo autoload.php que probablemente es parte de la configuración de Composer
require '../../../vendor/autoload.php';
// Incluir el archivo que contiene la lógica para la conexión a la base de datos
require_once '../../bd/bd.php';

// Configurar Twig para cargar plantillas desde el directorio especificado
$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/common/templates');
$twig = new \Twig\Environment($loader);

// Iniciar una sesión PHP para mantener y acceder a variables de sesión
session_start();

// Comprobar si se recibió el token por GET
if (isset($_GET['token']) && isset($_GET['email'])) {
    // Obtener el token de recuperación y el correo electrónico del GET
    $tokenRecuperacion = $_GET['token'];
    $correo = $_GET['email'];

    // Renderizar la plantilla 'recuperarContraseña.html' utilizando Twig, pasando el token y el correo
    echo $twig->render('recuperarContraseña.html', ['token' => $tokenRecuperacion, 'correo' => $correo]);

    // Si se ha enviado una solicitud POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Crear una instancia de la clase BD para interactuar con la base de datos
        $bd = new BD();
        // Iniciar una conexión a la base de datos
        $bd->iniciarConexion();

        // Obtener el token de recuperación correspondiente al correo electrónico de la base de datos
        $tokenBD = $bd->getTokenRecuperacion($correo);

        // Verificar si el token enviado por GET coincide con el token almacenado en la base de datos
        if ($bd->verificarTokenRecuperacion($tokenRecuperacion, $tokenBD)) {
            // Verificar si se han enviado los campos de contraseña y confirmación de contraseña
            if (isset($_POST['password']) && isset($_POST['confirm_password'])) {
                // Obtener la contraseña y la confirmación de contraseña del formulario
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirm_password'];

                // Verificar si las contraseñas coinciden
                if ($password == $confirmPassword) {
                    // Modificar la contraseña del usuario en la base de datos
                    if ($bd->modificarContraseñaUsuario($correo, $password)) {
                        echo 'Contraseña modificada con éxito';
                        // Redirigir al usuario a la página de inicio de sesión
                        header("Location: ../sesiones/login.php");
                    } else {
                        echo 'Error al modificar la contraseña';
                    }
                } else {
                    echo 'Las contraseñas no coinciden';
                }
            }
        }

        // Cerrar la conexión a la base de datos
        $bd->cerrarConexion();
    }
} else {
    // No se recibió el token por GET
    echo 'No se recibió el token de recuperación de contraseña.';
}

<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require '../../../vendor/autoload.php';
require_once '../../bd/bd.php';

session_start();

$loader = new \Twig\Loader\FilesystemLoader('../../../frontend/common/templates');
$twig = new \Twig\Environment($loader);

echo $twig->render('enviaEmail.html', []);

$bd = new BD();
$bd->iniciarConexion();

// Verificar si se está realizando una petición POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se recibieron los datos del formulario
    if (isset($_POST['email'])) {
        // Obtener el correo electrónico y el nombre de usuario (nickName) desde el formulario
        $email = $_POST['email'];

        $usuario = $bd->getUsuarioPorCorreo($email);

        // Verificar si se encontró un usuario con el nickName dado
        if ($usuario) {
            // Obtener el correo electrónico del usuario
            $correoDestino = $email;
            $nombreDestino = $usuario->getNickName();

            //echo var_dump($usuario);

            //echo var_dump($usuario->getNickName());

            // Datos del correo electrónico
            $asunto = 'Recuperación de Contraseña';
            $asuntoCodificado = mb_encode_mimeheader($asunto, 'UTF-8');
            $tokenRecuperacion = $bd->generarTokenRecuperacion();

            //echo $tokenRecuperacion;

            if ($bd->insertarTokenRecuperacion($nombreDestino, $tokenRecuperacion)) {

                // Contenido del correo
                $mensaje = $mensaje = '
                <html lang="es">
                <head>
                    <meta charset="UTF-8" />
                    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
                </head>
                <header>
                    <h1>Recuperación de Contraseña</h1>
                </header>
                <body>
                <p>Hola,</p>
                <p>Has solicitado recuperar tu contraseña. Haz clic en el siguiente enlace para restablecerla:</p>
                <p><a href="http://localhost/quickalive/backend/api/recuContraseña/recuperarContraseña.php?token=' . $tokenRecuperacion . '&email=' . $email . '">Restablecer Contraseña</a></p>
                <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
                <p>Saludos,</p>
                <p>Tu Equipo</p>
                </body>
                </html>
                ';

                // Configuración de PHPMailer
                $mail = new PHPMailer(true);

                try {
                    // Configuración del servidor SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'servicio.recuperacion.quickalive@gmail.com'; //getenv('CORREO_GMAIL'); // Cambiar por tu correo
                    $mail->Password = 'fgmf pirk pvjn obfr'; //getenv('CONTRASENA_GMAIL'); // Cambiar por tu contraseña

                    //echo var_dump($mail->Password);
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    // Configuración del correo
                    $mail->setFrom($mail->Username, 'QuickAlive');
                    $mail->addAddress($email); // Cambiar por la dirección de correo ingresada en el formulario
                    $mail->isHTML(true);
                    $mail->Subject = $asuntoCodificado;
                    $mail->Body = $mensaje;

                    // Envío del correo
                    $mail->send();
                    echo 'El correo se ha enviado correctamente';

                } catch (Exception $e) {
                    echo "Error al enviar el correo: {$mail->ErrorInfo}";
                }
            }
        } else {
            echo "No se encontró un usuario con el nombre de usuario proporcionado.";
        }
    } else {
        echo "Por favor, completa todos los campos del formulario.";
    }
}

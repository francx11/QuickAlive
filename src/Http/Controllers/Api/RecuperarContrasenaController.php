<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Usuario\UsuarioRepositoryInterface;
use App\Infrastructure\Mail\PhpMailerMailer;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class RecuperarContrasenaController
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarios,
        private PhpMailerMailer $mailer,
        private TwigResponder $twig,
    ) {}

    public function enviaEmail(ServerRequestInterface $request): ResponseInterface
    {
        $respuestaFormulario = $this->twig->render('enviaEmail.html');

        if ($request->getMethod() !== 'POST') {
            return $respuestaFormulario;
        }

        $email = $request->getParsedBody()['email'] ?? null;

        if ($email === null) {
            return $respuestaFormulario;
        }

        $usuario = $this->usuarios->porCorreo($email);

        if ($usuario === null) {
            return $respuestaFormulario;
        }

        $token = $this->usuarios->generarTokenRecuperacion();
        $this->usuarios->insertarTokenRecuperacion($usuario->nickName, $token);

        $enlace = "http://localhost/quickalive/backend/api/recuContraseña/recuperarContraseña.php?token={$token}&email={$email}";
        $mensaje = <<<HTML
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
            <p><a href="{$enlace}">Restablecer Contraseña</a></p>
            <p>Si no solicitaste este cambio, puedes ignorar este correo.</p>
            <p>Saludos,</p>
            <p>Tu Equipo</p>
            </body>
            </html>
            HTML;

        $this->mailer->enviar($email, 'Recuperación de Contraseña', $mensaje);

        return $respuestaFormulario;
    }

    public function recuperarContrasena(ServerRequestInterface $request): ResponseInterface
    {
        $parametros = $request->getQueryParams();

        if (!isset($parametros['token'], $parametros['email'])) {
            return $this->twig->render('recuperarContraseña.html', ['token' => null, 'correo' => null]);
        }

        $token = $parametros['token'];
        $correo = $parametros['email'];

        $respuestaFormulario = $this->twig->render('recuperarContraseña.html', ['token' => $token, 'correo' => $correo]);

        if ($request->getMethod() !== 'POST') {
            return $respuestaFormulario;
        }

        $tokenBD = $this->usuarios->tokenRecuperacion($correo);

        if ($tokenBD === null || $token !== $tokenBD) {
            return $respuestaFormulario;
        }

        $datos = $request->getParsedBody();

        if (!isset($datos['password'], $datos['confirm_password']) || $datos['password'] !== $datos['confirm_password']) {
            return $respuestaFormulario;
        }

        $this->usuarios->modificarContrasena($correo, $datos['password']);

        return $respuestaFormulario;
    }
}

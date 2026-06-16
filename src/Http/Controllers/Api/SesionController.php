<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Usuario\UsuarioRepositoryInterface;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class SesionController
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarios,
        private TwigResponder $responder,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function login(ServerRequestInterface $request): ResponseInterface
    {
        $inicio = 0;

        if ($request->getMethod() === 'POST') {
            $datos = $request->getParsedBody();
            $nickName = $datos['nickName'] ?? '';
            $password = $datos['password'] ?? '';

            if ($this->usuarios->checkLogin($nickName, $password)) {
                $usuario = $this->usuarios->porNickName($nickName);

                $_SESSION['nickName'] = $usuario->nickName;
                $_SESSION['loggedin'] = true;
                $_SESSION['rol'] = $usuario->rol;
                $_SESSION['idUsuario'] = $usuario->idUsuario;

                $destino = $usuario->rol === 'root'
                    ? '/backend/admin/panelAdmin.php'
                    : '/backend/user/pantallaInicial.php';

                return $this->responseFactory->createResponse(302)->withHeader('Location', $destino);
            }

            $inicio = 2;
        }

        return $this->responder->render('login.html', ['inicio' => $inicio]);
    }

    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $_SESSION = [];
        session_destroy();

        return $this->responseFactory->createResponse(302)->withHeader('Location', '/index.php');
    }
}

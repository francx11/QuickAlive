<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Domain\Usuario\UsuarioRepositoryInterface;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class CuentaController
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarios,
        private TwigResponder $twig,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function pantallaInicial(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];

        return $this->twig->render('pantallaInicial.html', [
            'esPremium' => $this->usuarios->esPremium($idUsuario),
        ]);
    }

    public function eliminarCuenta(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];

        if (!$this->usuarios->eliminar($idUsuario)) {
            return $this->responseFactory->createResponse(500)->withHeader('Content-Type', 'text/plain');
        }

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', '/backend/api/sesiones/logout.php');
    }
}

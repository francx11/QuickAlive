<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Domain\Usuario\ActualizarUsuarioDTO;
use App\Domain\Usuario\NuevoUsuarioDTO;
use App\Domain\Usuario\UsuarioRepositoryInterface;
use App\Infrastructure\View\JsonResponder;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class UsuarioController
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarios,
        private TwigResponder $twig,
        private JsonResponder $json,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function index(ServerRequestInterface $request): ResponseInterface
    {
        return $this->twig->render('gestionUsuarios.html', ['logueado' => true]);
    }

    public function alta(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getMethod() === 'POST') {
            $datos = $request->getParsedBody();

            $this->usuarios->insertar(new NuevoUsuarioDTO(
                nickName: $datos['nickName'],
                telefono: $datos['telefono'],
                correo: $datos['correo'],
                password: $datos['password'],
                nombre: $datos['nombre'],
                apellidos: $datos['apellidos'],
                edad: (int) $datos['edad'],
                rol: $datos['rol'],
            ));

            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', '/backend/admin/gestionUsuarios/gestionUsuarios.php');
        }

        return $this->twig->render('altaUsuario.html', ['logueado' => true]);
    }

    public function buscar(ServerRequestInterface $request): ResponseInterface
    {
        $datos = $request->getParsedBody();
        $nickNameBuscado = $datos['nickNameBuscado'] ?? null;

        if ($nickNameBuscado === null) {
            return $this->json->render([]);
        }

        return $this->json->render($this->usuarios->buscarCoincidencias($nickNameBuscado));
    }

    public function eliminar(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) ($request->getQueryParams()['id'] ?? -1);

        if (!$this->usuarios->eliminar($idUsuario)) {
            return $this->responseFactory->createResponse(500)
                ->withHeader('Content-Type', 'text/plain');
        }

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', '/backend/admin/gestionUsuarios/gestionUsuarios.php');
    }

    public function modificar(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) ($request->getQueryParams()['id'] ?? -1);
        $usuarioOriginal = $this->usuarios->porId($idUsuario);

        if ($request->getMethod() === 'POST' && $usuarioOriginal !== null) {
            $datos = $request->getParsedBody();

            $valorOMantener = static fn (string $clave, string $actual): string => ($datos[$clave] ?? '') !== ''
                ? $datos[$clave]
                : $actual;

            $this->usuarios->modificar(new ActualizarUsuarioDTO(
                idUsuario: $idUsuario,
                nickName: $valorOMantener('nickName', $usuarioOriginal->nickName),
                telefono: $valorOMantener('telefono', $usuarioOriginal->telefono),
                correo: $valorOMantener('correo', $usuarioOriginal->correo),
                password: ($datos['password'] ?? '') !== '' ? $datos['password'] : null,
                nombre: $valorOMantener('nombre', $usuarioOriginal->nombre),
                apellidos: $valorOMantener('apellidos', $usuarioOriginal->apellidos),
                edad: (int) $valorOMantener('edad', (string) $usuarioOriginal->edad),
                rol: $valorOMantener('rol', $usuarioOriginal->rol),
            ));

            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', '/backend/admin/gestionUsuarios/gestionUsuarios.php');
        }

        return $this->twig->render('modificarUsuario.html', ['idUsuario' => $idUsuario, 'logueado' => true]);
    }
}

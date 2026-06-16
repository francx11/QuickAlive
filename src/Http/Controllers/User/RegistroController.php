<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Domain\Preferencia\PreferenciaRepositoryInterface;
use App\Domain\Usuario\NuevoUsuarioDTO;
use App\Domain\Usuario\UsuarioRepositoryInterface;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class RegistroController
{
    public function __construct(
        private UsuarioRepositoryInterface $usuarios,
        private PreferenciaRepositoryInterface $preferencias,
        private TwigResponder $twig,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function registroBasico(ServerRequestInterface $request): ResponseInterface
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
                rol: $datos['role'],
            ));

            $usuario = $this->usuarios->porNickName($datos['nickName']);

            return $this->responseFactory->createResponse(302)
                ->withHeader('Location', '/backend/user/gestionRegistro/registroPreferencias.php?id=' . $usuario->idUsuario);
        }

        return $this->twig->render('user/gestionRegistro/registroBasico.html');
    }

    public function registroPreferencias(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) ($request->getQueryParams()['id'] ?? -1);

        return $this->twig->render('user/gestionRegistro/registroPreferencias.html', [
            'tiposPreferencias' => $this->preferencias->todosLosTipos(),
            'idUsuario' => $idUsuario,
        ]);
    }

    public function guardarPreferencias(ServerRequestInterface $request): ResponseInterface
    {
        $datos = $request->getParsedBody();
        $idUsuario = (int) ($datos['idUsuario'] ?? -1);

        foreach ($datos['preferencias'] ?? [] as $preferencia) {
            $this->preferencias->insertarPreferenciaPersonal(
                $idUsuario,
                $preferencia['nombreTipoPreferencia'],
                (int) $preferencia['idTipoPreferencia'],
            );
        }

        return $this->responseFactory->createResponse(204);
    }

    public function renderActualizarPreferencias(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];

        return $this->twig->render('user/gestionRegistro/actualizarPreferencias.html', [
            'tiposPreferencias' => $this->preferencias->todosLosTipos(),
            'preferenciasAnteriores' => $this->preferencias->preferenciasUsuario($idUsuario),
            'idUsuario' => $idUsuario,
        ]);
    }

    public function actualizarPreferencias(ServerRequestInterface $request): ResponseInterface
    {
        $datos = $request->getParsedBody();
        $idUsuario = (int) ($datos['idUsuario'] ?? -1);

        $preferencias = array_map(
            static fn (array $preferencia): array => [
                'nombreTipoPreferencia' => $preferencia['nombreTipoPreferencia'],
                'idTipoPreferencia' => (int) $preferencia['idTipoPreferencia'],
            ],
            $datos['preferencias'] ?? [],
        );

        $this->preferencias->actualizarPreferenciasPersonales($idUsuario, $preferencias);

        return $this->responseFactory->createResponse(204);
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Application\Actividad\RevertirActividadCompletadaService;
use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class HistorialController
{
    public function __construct(
        private ActividadRepositoryInterface $actividades,
        private RevertirActividadCompletadaService $revertirActividad,
        private TwigResponder $twig,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function render(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];

        $listaActividades = [];

        foreach ($this->actividades->historialPorUsuario($idUsuario) as $actividadRealizada) {
            $actividad = $this->actividades->porId((int) $actividadRealizada['idActividad']);

            if ($actividad === null) {
                continue;
            }

            $listaActividades[] = [
                'idActividad' => $actividadRealizada['idActividad'],
                'nombreActividad' => $actividad->nombreActividad,
                'descripcion' => $actividad->descripcion,
                'duracion' => $actividad->duracion,
                'tipoActividad' => $actividad->tipoActividad,
                'fechaRealizacion' => $actividadRealizada['fechaRealizacion'],
                'completada' => $actividadRealizada['completada'],
            ];
        }

        return $this->twig->render('gestionHistorialActividades/historialActividades.html', [
            'listaActividades' => $listaActividades,
            'idUsuario' => $idUsuario,
        ]);
    }

    public function volverARealizar(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];
        $datos = $request->getParsedBody();
        $idActividad = (int) ($datos['idActividad'] ?? -1);
        $estado = $datos['estado'] ?? '';

        if ($estado !== 'aceptada') {
            return $this->responseFactory->createResponse(400)->withHeader('Content-Type', 'text/plain');
        }

        if (!$this->revertirActividad->ejecutar($idUsuario, $idActividad, $estado)) {
            return $this->responseFactory->createResponse(500)->withHeader('Content-Type', 'text/plain');
        }

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', '/backend/user/gestionHistorialActividades/renderHistorialActividades.php');
    }
}

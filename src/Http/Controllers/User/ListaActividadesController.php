<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Infrastructure\View\TwigResponder;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ListaActividadesController
{
    public function __construct(
        private ActividadRepositoryInterface $actividades,
        private TwigResponder $twig,
        private ResponseFactoryInterface $responseFactory,
    ) {}

    public function render(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];

        $listaActividades = [];

        foreach ($this->actividades->pendientesPorUsuario($idUsuario) as $pendiente) {
            $actividad = $this->actividades->porId($pendiente['idActividad']);

            if ($actividad === null) {
                continue;
            }

            $listaActividades[] = [
                'idActividad' => $pendiente['idActividad'],
                'nombreActividad' => $actividad->nombreActividad,
                'descripcion' => $actividad->descripcion,
                'duracion' => $actividad->duracion,
                'tipoActividad' => $actividad->tipoActividad,
                'fechaRealizacion' => $pendiente['fechaRealizacion'],
                'completada' => $pendiente['completada'],
            ];
        }

        return $this->twig->render('gestionListaActividades/listaActividades.html', [
            'listaActividades' => $listaActividades,
            'idUsuario' => $idUsuario,
        ]);
    }

    public function marcarCompletada(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];
        $idActividad = (int) ($request->getParsedBody()['idActividad'] ?? -1);

        if (!$this->actividades->completar($idUsuario, $idActividad)) {
            return $this->responseFactory->createResponse(500)->withHeader('Content-Type', 'text/plain');
        }

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', '/backend/user/gestionListaActividades/renderListaActividades.php');
    }

    public function modificarFechaRealizacion(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];
        $datos = $request->getParsedBody();
        $idActividad = (int) ($datos['idActividad'] ?? -1);
        $nuevaFecha = $datos['nuevaFecha'] ?? '';

        if (!$this->actividades->modificarFechaRealizacion($idUsuario, $idActividad, $nuevaFecha)) {
            return $this->responseFactory->createResponse(500)->withHeader('Content-Type', 'text/plain');
        }

        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', '/backend/user/gestionListaActividades/renderListaActividades.php');
    }
}

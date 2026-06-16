<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Application\Actividad\DecidirActividadService;
use App\Application\Actividad\RecomendarActividadesService;
use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Domain\Actividad\NuevaActividadGeolocalizableDTO;
use App\Infrastructure\View\JsonResponder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class RecomendacionesController
{
    public function __construct(
        private ActividadRepositoryInterface $actividades,
        private DecidirActividadService $decidirActividad,
        private RecomendarActividadesService $recomendaciones,
        private JsonResponder $json,
    ) {}

    public function decidirInteresActividad(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];
        $parametros = $request->getQueryParams();
        $idActividad = (int) ($parametros['idActividad'] ?? -1);
        $estado = $parametros['estado'] ?? '';

        $this->decidirActividad->decidir($idUsuario, $idActividad, $estado);

        return $this->json->render(['status' => 'success', 'message' => 'Actividad actualizada correctamente']);
    }

    public function insertarYDecidirGeolocalizable(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];
        $parametros = $request->getQueryParams();

        $idActividad = $this->actividades->insertarGeolocalizable(new NuevaActividadGeolocalizableDTO(
            nombreActividad: $parametros['nombreActividad'],
            descripcion: $parametros['descripcion'],
            duracion: 0,
            urlImagen: $parametros['urlRemota'],
            idApi: $parametros['idApi'],
            fechaLimite: $parametros['fechaRealizacion'],
        ));

        $estado = $parametros['estado'] ?? '';

        if ($estado === 'aceptada') {
            $this->actividades->marcarRealizada($idUsuario, $idActividad);
            $actividadGeo = $this->actividades->geolocalizablePorId($idActividad);
            $this->actividades->modificarFechaRealizacion($idUsuario, $idActividad, $actividadGeo->fechaLimite);
        } elseif ($estado === 'rechazada') {
            $this->actividades->rechazar($idUsuario, $idActividad);
        }

        return $this->json->render(['status' => 'success', 'message' => 'Actividad insertada correctamente']);
    }

    public function insertarGeolocalizable(ServerRequestInterface $request): ResponseInterface
    {
        $datos = $request->getParsedBody();

        $idActividad = $this->actividades->insertarGeolocalizable(new NuevaActividadGeolocalizableDTO(
            nombreActividad: $datos['nombreActividad'],
            descripcion: $datos['descripcion'],
            duracion: (int) $datos['duracion'],
            urlImagen: $datos['urlImagen'],
            idApi: $datos['idApi'],
            fechaLimite: $datos['fechaLimite'],
        ));

        return $this->json->render(['status' => 'success', 'idActividad' => $idActividad]);
    }

    public function obtenerGeolocalizablesDisponibles(ServerRequestInterface $request): ResponseInterface
    {
        $disponibles = $this->actividades->geolocalizablesDisponibles();

        if ($disponibles === []) {
            return $this->json->render(['message' => 'No se encontraron actividades geolocalizables para el usuario.']);
        }

        return $this->json->render($disponibles);
    }

    public function obtenerSimplesRecomendadas(ServerRequestInterface $request): ResponseInterface
    {
        $idUsuario = (int) $_SESSION['idUsuario'];

        $recomendadas = array_values(array_filter(
            $this->recomendaciones->paraUsuario($idUsuario),
            static fn (array $actividad): bool => $actividad['tipoActividad'] !== 'geolocalizable',
        ));

        if ($recomendadas === []) {
            return $this->json->render(['message' => 'No se encontraron actividades recomendadas para el usuario.']);
        }

        return $this->json->render($recomendadas);
    }
}

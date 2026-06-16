<?php

declare(strict_types=1);

namespace App\Domain\Actividad;

final class ActividadGeolocalizable extends Actividad
{
    public function __construct(
        int $idActividad,
        string $nombreActividad,
        string $descripcion,
        int $duracion,
        string $tipoActividad,
        public readonly string $urlRemota,
        public readonly string $idApi,
        public readonly string $fechaLimite,
    ) {
        parent::__construct($idActividad, $nombreActividad, $descripcion, $duracion, $tipoActividad);
    }
}

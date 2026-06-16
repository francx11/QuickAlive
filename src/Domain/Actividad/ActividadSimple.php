<?php

declare(strict_types=1);

namespace App\Domain\Actividad;

final class ActividadSimple extends Actividad
{
    /** @param string[] $galeriaFotos */
    public function __construct(
        int $idActividad,
        string $nombreActividad,
        string $descripcion,
        int $duracion,
        string $tipoActividad,
        public readonly array $galeriaFotos = [],
    ) {
        parent::__construct($idActividad, $nombreActividad, $descripcion, $duracion, $tipoActividad);
    }
}

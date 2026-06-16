<?php

declare(strict_types=1);

namespace App\Domain\Actividad;

class Actividad
{
    public function __construct(
        public readonly int $idActividad,
        public readonly string $nombreActividad,
        public readonly string $descripcion,
        public readonly int $duracion,
        public readonly string $tipoActividad,
    ) {}
}

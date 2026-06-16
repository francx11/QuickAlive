<?php

declare(strict_types=1);

namespace App\Domain\Actividad;

final readonly class NuevaActividadSimpleDTO
{
    public function __construct(
        public string $nombreActividad,
        public string $descripcion,
        public int $duracion,
    ) {}
}

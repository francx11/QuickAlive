<?php

declare(strict_types=1);

namespace App\Domain\Actividad;

final readonly class NuevaActividadGeolocalizableDTO
{
    public function __construct(
        public string $nombreActividad,
        public string $descripcion,
        public int $duracion,
        public string $urlImagen,
        public string $idApi,
        public string $fechaLimite,
    ) {}
}

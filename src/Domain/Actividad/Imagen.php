<?php

declare(strict_types=1);

namespace App\Domain\Actividad;

final readonly class Imagen
{
    public function __construct(
        public int $numImagen,
        public int $idActividad,
        public string $url,
    ) {}
}

<?php

declare(strict_types=1);

namespace App\Domain\Preferencia;

final readonly class TipoPreferencia
{
    public function __construct(
        public int $idTipoPreferencia,
        public string $tipoPreferencia,
    ) {}
}

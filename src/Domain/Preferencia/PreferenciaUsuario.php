<?php

declare(strict_types=1);

namespace App\Domain\Preferencia;

final readonly class PreferenciaUsuario
{
    public function __construct(
        public int $idUsuario,
        public int $idTipoPreferencia,
        public string $nombreTipoPreferencia,
        public int $pInteres,
    ) {}
}

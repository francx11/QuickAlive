<?php

declare(strict_types=1);

namespace App\Domain\Usuario;

final readonly class NuevoUsuarioDTO
{
    public function __construct(
        public string $nickName,
        public string $telefono,
        public string $correo,
        public string $password,
        public string $nombre,
        public string $apellidos,
        public int $edad,
        public string $rol,
    ) {}
}

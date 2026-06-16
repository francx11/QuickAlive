<?php

declare(strict_types=1);

namespace App\Domain\Usuario;

final readonly class Usuario
{
    public function __construct(
        public int $idUsuario,
        public string $nickName,
        public string $telefono,
        public string $correo,
        public string $password,
        public string $nombre,
        public string $apellidos,
        public int $edad,
        public string $rol,
        public bool $isPremium = false,
    ) {}

    /** @param array<string, mixed> $row */
    public static function fromRow(array $row): self
    {
        return new self(
            idUsuario: (int) $row['idUsuario'],
            nickName: $row['nickName'],
            telefono: $row['telefono'],
            correo: $row['correo'],
            password: $row['password'],
            nombre: $row['nombre'],
            apellidos: $row['apellidos'],
            edad: (int) $row['edad'],
            rol: $row['rol'],
            isPremium: (bool) ($row['isPremium'] ?? false),
        );
    }
}

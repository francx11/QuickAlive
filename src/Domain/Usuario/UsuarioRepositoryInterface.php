<?php

declare(strict_types=1);

namespace App\Domain\Usuario;

interface UsuarioRepositoryInterface
{
    public function porId(int $idUsuario): ?Usuario;

    public function porCorreo(string $correo): ?Usuario;

    public function porNickName(string $nickName): ?Usuario;

    public function insertar(NuevoUsuarioDTO $datos): bool;

    public function modificar(ActualizarUsuarioDTO $datos): bool;

    public function modificarContrasena(string $correo, string $nuevaContrasena): bool;

    public function eliminar(int $idUsuario): bool;

    public function checkLogin(string $nickName, string $password): bool;

    /** @return array<int, array{idUsuario: int, nickName: string, correo: string, nombre: string, apellidos: string}> */
    public function buscarCoincidencias(string $nickName): array;

    public function esPremium(int $idUsuario): bool;

    public function activarPremiumDemo(int $idUsuario): bool;

    public function tokenRecuperacion(string $correo): ?string;

    public function insertarTokenRecuperacion(string $nickName, string $tokenRecuperacion): bool;

    public function generarTokenRecuperacion(): string;
}

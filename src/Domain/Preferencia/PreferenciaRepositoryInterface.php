<?php

declare(strict_types=1);

namespace App\Domain\Preferencia;

interface PreferenciaRepositoryInterface
{
    public function insertarTipo(string $tipoPreferencia): int;

    public function nombreTipo(int $idTipoPreferencia): ?string;

    public function eliminarTipo(int $idTipoPreferencia): bool;

    /** @return array<int, array{idTipoPreferencia: int, tipoPreferencia: string}> */
    public function buscarCoincidenciasTipo(string $tipoPreferencia): array;

    /** @return TipoPreferencia[] */
    public function todosLosTipos(): array;

    /** @return PreferenciaUsuario[]|null */
    public function preferenciasUsuario(int $idUsuario): ?array;

    public function insertarPreferenciaPersonal(int $idUsuario, string $nombreTipoPreferencia, int $idTipoPreferencia): bool;

    public function eliminarPreferenciaPersonal(int $idUsuario, string $nombreTipoPreferencia, int $idTipoPreferencia): bool;

    public function eliminarPreferenciasPersonalesUsuario(int $idUsuario): bool;

    /** @param array<int, array{nombreTipoPreferencia: string, idTipoPreferencia: int}> $preferencias */
    public function actualizarPreferenciasPersonales(int $idUsuario, array $preferencias): void;

    /** @return int[] */
    public function idsTipoPreferenciaFaltantes(int $idUsuario): array;

    public function actualizarPuntoInteres(int $idTipoPreferencia, int $nuevoPInteres): bool;
}

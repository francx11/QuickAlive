<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mysql;

use App\Domain\Preferencia\PreferenciaRepositoryInterface;
use App\Domain\Preferencia\PreferenciaUsuario;
use App\Domain\Preferencia\TipoPreferencia;
use Doctrine\DBAL\Connection;

final readonly class PreferenciaRepository implements PreferenciaRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function insertarTipo(string $tipoPreferencia): int
    {
        $this->connection->insert('tipopreferencias', ['tipoPreferencia' => $tipoPreferencia]);

        return (int) $this->connection->lastInsertId();
    }

    public function nombreTipo(int $idTipoPreferencia): ?string
    {
        $nombre = $this->connection->fetchOne(
            'SELECT tipoPreferencia FROM tipopreferencias WHERE idTipoPreferencia = ?',
            [$idTipoPreferencia],
        );

        return $nombre === false ? null : $nombre;
    }

    public function eliminarTipo(int $idTipoPreferencia): bool
    {
        return $this->connection->delete('tipopreferencias', ['idTipoPreferencia' => $idTipoPreferencia]) > 0;
    }

    public function buscarCoincidenciasTipo(string $tipoPreferencia): array
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT idTipoPreferencia, tipoPreferencia FROM tipopreferencias WHERE tipoPreferencia LIKE CONCAT('%', ?, '%')",
            [$tipoPreferencia],
        );

        return array_map(
            static fn (array $row): array => [
                'idTipoPreferencia' => (int) $row['idTipoPreferencia'],
                'tipoPreferencia' => $row['tipoPreferencia'],
            ],
            $rows,
        );
    }

    public function todosLosTipos(): array
    {
        $rows = $this->connection->fetchAllAssociative('SELECT idTipoPreferencia, tipoPreferencia FROM tipopreferencias');

        return array_map(
            static fn (array $row): TipoPreferencia => new TipoPreferencia((int) $row['idTipoPreferencia'], $row['tipoPreferencia']),
            $rows,
        );
    }

    public function preferenciasUsuario(int $idUsuario): ?array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT idUsuario, idTipoPreferencia, nombreTipoPreferencia, pInteres FROM usuariopreferencias WHERE idUsuario = ?',
            [$idUsuario],
        );

        if ($rows === []) {
            return null;
        }

        return array_map(
            static fn (array $row): PreferenciaUsuario => new PreferenciaUsuario(
                (int) $row['idUsuario'],
                (int) $row['idTipoPreferencia'],
                $row['nombreTipoPreferencia'],
                (int) $row['pInteres'],
            ),
            $rows,
        );
    }

    public function insertarPreferenciaPersonal(int $idUsuario, string $nombreTipoPreferencia, int $idTipoPreferencia): bool
    {
        $this->connection->insert('usuariopreferencias', [
            'idUsuario' => $idUsuario,
            'nombreTipoPreferencia' => $nombreTipoPreferencia,
            'idTipoPreferencia' => $idTipoPreferencia,
        ]);

        return true;
    }

    public function eliminarPreferenciaPersonal(int $idUsuario, string $nombreTipoPreferencia, int $idTipoPreferencia): bool
    {
        $affected = $this->connection->delete('usuariopreferencias', [
            'idUsuario' => $idUsuario,
            'nombreTipoPreferencia' => $nombreTipoPreferencia,
            'idTipoPreferencia' => $idTipoPreferencia,
        ]);

        return $affected > 0;
    }

    public function eliminarPreferenciasPersonalesUsuario(int $idUsuario): bool
    {
        return $this->connection->delete('usuariopreferencias', ['idUsuario' => $idUsuario]) > 0;
    }

    public function actualizarPreferenciasPersonales(int $idUsuario, array $preferencias): void
    {
        $this->eliminarPreferenciasPersonalesUsuario($idUsuario);

        foreach ($preferencias as $preferencia) {
            $this->insertarPreferenciaPersonal($idUsuario, $preferencia['nombreTipoPreferencia'], $preferencia['idTipoPreferencia']);
        }
    }

    public function idsTipoPreferenciaFaltantes(int $idUsuario): array
    {
        $ids = $this->connection->fetchFirstColumn(
            'SELECT idTipoPreferencia FROM tipopreferencias WHERE idTipoPreferencia NOT IN (SELECT idTipoPreferencia FROM usuariopreferencias WHERE idUsuario = ?)',
            [$idUsuario],
        );

        return array_map(intval(...), $ids);
    }

    public function actualizarPuntoInteres(int $idTipoPreferencia, int $nuevoPInteres): bool
    {
        $affected = $this->connection->update(
            'usuariopreferencias',
            ['pInteres' => $nuevoPInteres],
            ['idTipoPreferencia' => $idTipoPreferencia],
        );

        return $affected > 0;
    }
}

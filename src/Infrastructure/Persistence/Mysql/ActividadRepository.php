<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Mysql;

use App\Domain\Actividad\ActividadGeolocalizable;
use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Domain\Actividad\ActividadSimple;
use App\Domain\Actividad\Imagen;
use App\Domain\Actividad\NuevaActividadGeolocalizableDTO;
use App\Domain\Actividad\NuevaActividadSimpleDTO;
use Doctrine\DBAL\Connection;

final readonly class ActividadRepository implements ActividadRepositoryInterface
{
    public function __construct(
        private Connection $connection,
    ) {}

    public function insertarSimple(NuevaActividadSimpleDTO $datos): int
    {
        return $this->connection->transactional(function () use ($datos): int {
            $this->connection->insert('actividad', [
                'nombreActividad' => $datos->nombreActividad,
                'descripcion' => $datos->descripcion,
                'duracion' => $datos->duracion,
                'tipoActividad' => 'simple',
            ]);
            $idActividad = (int) $this->connection->lastInsertId();

            $this->connection->insert('actividadsimple', ['idActividad' => $idActividad]);

            return $idActividad;
        });
    }

    public function insertarGeolocalizable(NuevaActividadGeolocalizableDTO $datos): int
    {
        return $this->connection->transactional(function () use ($datos): int {
            $this->connection->insert('actividad', [
                'nombreActividad' => $datos->nombreActividad,
                'descripcion' => $datos->descripcion,
                'duracion' => $datos->duracion,
                'tipoActividad' => 'geolocalizable',
            ]);
            $idActividad = (int) $this->connection->lastInsertId();

            $this->connection->insert('actividadgeolocalizable', [
                'idActividad' => $idActividad,
                'urlRemota' => $datos->urlImagen,
                'idApi' => $datos->idApi,
                'fechaLimite' => $datos->fechaLimite,
            ]);

            return $idActividad;
        });
    }

    public function insertarCategorias(int $idActividad, array $idsTipoPreferencia): bool
    {
        foreach ($idsTipoPreferencia as $idTipoPreferencia) {
            $this->connection->insert('actividad_tipopreferencia', [
                'idActividad' => $idActividad,
                'idTipoPreferencia' => $idTipoPreferencia,
            ]);
        }

        return true;
    }

    public function modificar(int $idActividad, string $nombreActividad, string $descripcion, int $duracion): bool
    {
        $affected = $this->connection->update(
            'actividad',
            ['nombreActividad' => $nombreActividad, 'descripcion' => $descripcion, 'duracion' => $duracion],
            ['idActividad' => $idActividad],
        );

        return $affected > 0;
    }

    public function modificarTipoPreferencias(int $idActividad, array $idsTipoPreferencia): bool
    {
        $this->connection->delete('actividad_tipopreferencia', ['idActividad' => $idActividad]);

        return $this->insertarCategorias($idActividad, $idsTipoPreferencia);
    }

    public function eliminar(int $idActividad): bool
    {
        $this->connection->delete('galeriafotos', ['idActividad' => $idActividad]);

        return $this->connection->delete('actividad', ['idActividad' => $idActividad]) > 0;
    }

    public function eliminarFotoGaleria(int $numImagen): bool
    {
        return $this->connection->delete('galeriafotos', ['numImagen' => $numImagen]) > 0;
    }

    public function agregarFotoGaleria(int $idActividad, string $foto): int
    {
        $this->connection->insert('galeriafotos', ['idActividad' => $idActividad, 'url' => $foto]);

        return (int) $this->connection->lastInsertId();
    }

    public function buscarCoincidencias(string $nombreActividad): array
    {
        $rows = $this->connection->fetchAllAssociative(
            "SELECT idActividad, nombreActividad, descripcion, duracion FROM actividad WHERE nombreActividad LIKE CONCAT('%', ?, '%') AND tipoActividad = 'simple'",
            [$nombreActividad],
        );

        return array_map(
            static fn (array $row): array => [
                'idActividad' => (int) $row['idActividad'],
                'nombreActividad' => $row['nombreActividad'],
                'descripcion' => $row['descripcion'],
                'duracion' => (int) $row['duracion'],
            ],
            $rows,
        );
    }

    public function porId(int $idActividad): ?ActividadSimple
    {
        $row = $this->connection->fetchAssociative(
            'SELECT idActividad, nombreActividad, descripcion, duracion, tipoActividad FROM actividad WHERE idActividad = ?',
            [$idActividad],
        );

        if ($row === false) {
            return null;
        }

        $urls = $this->connection->fetchFirstColumn(
            'SELECT url FROM galeriafotos WHERE idActividad = ?',
            [$idActividad],
        );

        return new ActividadSimple(
            (int) $row['idActividad'],
            $row['nombreActividad'],
            $row['descripcion'],
            (int) $row['duracion'],
            $row['tipoActividad'],
            $urls,
        );
    }

    public function geolocalizablePorId(int $idActividad): ?ActividadGeolocalizable
    {
        $row = $this->connection->fetchAssociative(
            'SELECT idActividad, nombreActividad, descripcion, duracion, tipoActividad FROM actividad WHERE idActividad = ?',
            [$idActividad],
        );

        if ($row === false) {
            return null;
        }

        $detalle = $this->connection->fetchAssociative(
            'SELECT urlRemota, idApi, fechaLimite FROM actividadgeolocalizable WHERE idActividad = ?',
            [$idActividad],
        );

        if ($detalle === false) {
            return null;
        }

        return new ActividadGeolocalizable(
            (int) $row['idActividad'],
            $row['nombreActividad'],
            $row['descripcion'],
            (int) $row['duracion'],
            $row['tipoActividad'],
            $detalle['urlRemota'],
            $detalle['idApi'],
            $detalle['fechaLimite'],
        );
    }

    public function galeria(int $idActividad): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT numImagen, idActividad, url FROM galeriafotos WHERE idActividad = ?',
            [$idActividad],
        );

        return array_map(
            static fn (array $row): Imagen => new Imagen((int) $row['numImagen'], (int) $row['idActividad'], $row['url']),
            $rows,
        );
    }

    public function marcarRealizada(int $idUsuario, int $idActividad): bool
    {
        $this->connection->insert('realiza', ['idUsuario' => $idUsuario, 'idActividad' => $idActividad]);

        return true;
    }

    public function eliminarRealizacion(int $idUsuario, int $idActividad): bool
    {
        return $this->connection->delete('realiza', ['idUsuario' => $idUsuario, 'idActividad' => $idActividad]) > 0;
    }

    public function modificarFechaRealizacion(int $idUsuario, int $idActividad, string $nuevaFechaHoraRealizacion): bool
    {
        $affected = $this->connection->update(
            'realiza',
            ['fechaRealizacion' => $nuevaFechaHoraRealizacion],
            ['idUsuario' => $idUsuario, 'idActividad' => $idActividad],
        );

        return $affected > 0;
    }

    public function pendientesPorUsuario(int $idUsuario): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT idActividad, fechaRealizacion, completada FROM realiza WHERE idUsuario = ? AND completada = 0',
            [$idUsuario],
        );

        return array_map(
            static fn (array $row): array => [
                'idActividad' => (int) $row['idActividad'],
                'fechaRealizacion' => $row['fechaRealizacion'],
                'completada' => (int) $row['completada'],
            ],
            $rows,
        );
    }

    public function geolocalizablesDisponibles(): array
    {
        $idsActividad = $this->connection->fetchFirstColumn(
            "SELECT idActividad FROM actividad WHERE tipoActividad = 'geolocalizable'",
        );

        $disponibles = [];

        foreach ($idsActividad as $idActividad) {
            $idActividad = (int) $idActividad;

            if ($this->estaEnRealiza($idActividad) || !$this->noEstaEnRechazadas($idActividad)) {
                $disponibles[] = [
                    'idActividad' => $idActividad,
                    'idApi' => $this->connection->fetchOne(
                        'SELECT idApi FROM actividadgeolocalizable WHERE idActividad = ?',
                        [$idActividad],
                    ) ?: null,
                ];
            }
        }

        return $disponibles;
    }

    public function completar(int $idUsuario, int $idActividad): bool
    {
        $affected = $this->connection->update(
            'realiza',
            ['completada' => 1, 'fechaRealizacion' => date('Y-m-d H:i:s')],
            ['idUsuario' => $idUsuario, 'idActividad' => $idActividad],
        );

        return $affected > 0;
    }

    public function descompletar(int $idUsuario, int $idActividad): bool
    {
        $affected = $this->connection->update(
            'realiza',
            ['completada' => 0, 'fechaRealizacion' => null],
            ['idUsuario' => $idUsuario, 'idActividad' => $idActividad],
        );

        return $affected > 0;
    }

    public function historialPorUsuario(int $idUsuario): array
    {
        return $this->connection->fetchAllAssociative(
            'SELECT * FROM realiza WHERE idUsuario = ? AND completada = 1',
            [$idUsuario],
        );
    }

    public function estaEnRealiza(int $idActividad): bool
    {
        $count = $this->connection->fetchOne('SELECT COUNT(*) FROM realiza WHERE idActividad = ?', [$idActividad]);

        return ((int) $count) > 0;
    }

    public function rechazar(int $idUsuario, int $idActividad): bool
    {
        $this->connection->insert('rechazadas', ['idUsuario' => $idUsuario, 'idActividad' => $idActividad]);

        return true;
    }

    public function noEstaEnRechazadas(int $idActividad): bool
    {
        $count = $this->connection->fetchOne('SELECT COUNT(*) FROM rechazadas WHERE idActividad = ?', [$idActividad]);

        return ((int) $count) === 0;
    }

    public function categorias(int $idActividad): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT idActividad, idTipoPreferencia FROM actividad_tipopreferencia WHERE idActividad = ?',
            [$idActividad],
        );

        return array_map(
            static fn (array $row): array => [
                'idActividad' => (int) $row['idActividad'],
                'idTipoPreferencia' => (int) $row['idTipoPreferencia'],
            ],
            $rows,
        );
    }

    public function todas(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT idActividad, nombreActividad, descripcion, duracion, tipoActividad FROM actividad',
        );

        return array_map(
            static fn (array $row): array => [
                'idActividad' => (int) $row['idActividad'],
                'nombreActividad' => $row['nombreActividad'],
                'descripcion' => $row['descripcion'],
                'duracion' => (int) $row['duracion'],
                'tipoActividad' => $row['tipoActividad'],
            ],
            $rows,
        );
    }

    public function todasConCategorias(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT idActividad, idTipoPreferencia FROM actividad_tipopreferencia',
        );

        return array_map(
            static fn (array $row): array => [
                'idActividad' => (int) $row['idActividad'],
                'idTipoPreferencia' => (int) $row['idTipoPreferencia'],
            ],
            $rows,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Actividad;

interface ActividadRepositoryInterface
{
    public function insertarSimple(NuevaActividadSimpleDTO $datos): int;

    public function insertarGeolocalizable(NuevaActividadGeolocalizableDTO $datos): int;

    /** @param int[] $idsTipoPreferencia */
    public function insertarCategorias(int $idActividad, array $idsTipoPreferencia): bool;

    public function modificar(int $idActividad, string $nombreActividad, string $descripcion, int $duracion): bool;

    /** @param int[] $idsTipoPreferencia */
    public function modificarTipoPreferencias(int $idActividad, array $idsTipoPreferencia): bool;

    public function eliminar(int $idActividad): bool;

    public function eliminarFotoGaleria(int $numImagen): bool;

    public function agregarFotoGaleria(int $idActividad, string $foto): int;

    /** @return array<int, array{idActividad: int, nombreActividad: string, descripcion: string, duracion: int}> */
    public function buscarCoincidencias(string $nombreActividad): array;

    public function porId(int $idActividad): ?ActividadSimple;

    public function geolocalizablePorId(int $idActividad): ?ActividadGeolocalizable;

    /** @return Imagen[] */
    public function galeria(int $idActividad): array;

    public function marcarRealizada(int $idUsuario, int $idActividad): bool;

    public function eliminarRealizacion(int $idUsuario, int $idActividad): bool;

    public function modificarFechaRealizacion(int $idUsuario, int $idActividad, string $nuevaFechaHoraRealizacion): bool;

    /** @return array<int, array{idActividad: int, fechaRealizacion: ?string, completada: int}> */
    public function pendientesPorUsuario(int $idUsuario): array;

    /** @return array<int, array{idActividad: int, idApi: ?string}> */
    public function geolocalizablesDisponibles(): array;

    public function completar(int $idUsuario, int $idActividad): bool;

    public function descompletar(int $idUsuario, int $idActividad): bool;

    /** @return array<int, array<string, mixed>> */
    public function historialPorUsuario(int $idUsuario): array;

    public function estaEnRealiza(int $idActividad): bool;

    public function rechazar(int $idUsuario, int $idActividad): bool;

    public function noEstaEnRechazadas(int $idActividad): bool;

    /** @return array<int, array{idActividad: int, idTipoPreferencia: int}> */
    public function categorias(int $idActividad): array;

    /** @return array<int, array{idActividad: int, nombreActividad: string, descripcion: string, duracion: int, tipoActividad: string}> */
    public function todas(): array;

    /** @return array<int, array{idActividad: int, idTipoPreferencia: int}> */
    public function todasConCategorias(): array;
}

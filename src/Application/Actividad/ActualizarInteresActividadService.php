<?php

declare(strict_types=1);

namespace App\Application\Actividad;

use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Domain\Preferencia\PreferenciaRepositoryInterface;
use App\Domain\Preferencia\PreferenciaUsuario;

/**
 * Shared core of BD::actualizarPuntosInteres() plus the "insert missing
 * preferences for an accepted activity's categories" logic that was
 * duplicated across volverArealizarActividad.php and
 * controladorInteresActividad.php in the legacy code.
 */
final readonly class ActualizarInteresActividadService
{
    public function __construct(
        private ActividadRepositoryInterface $actividades,
        private PreferenciaRepositoryInterface $preferencias,
    ) {}

    public function actualizar(int $idUsuario, int $idActividad, string $estado): void
    {
        $categoriasActividad = $this->actividades->categorias($idActividad);
        $preferenciasUsuario = $this->preferencias->preferenciasUsuario($idUsuario);

        if ($preferenciasUsuario !== null) {
            $this->actualizarPuntosInteres($categoriasActividad, $preferenciasUsuario, $estado);
        }

        if ($estado !== 'aceptada') {
            return;
        }

        $idsCategoria = array_column($categoriasActividad, 'idTipoPreferencia');
        $idsFaltantes = $preferenciasUsuario === null
            ? $idsCategoria
            : array_diff($idsCategoria, array_map(static fn (PreferenciaUsuario $p): int => $p->idTipoPreferencia, $preferenciasUsuario));

        foreach ($idsFaltantes as $idTipoPreferencia) {
            $nombre = $this->preferencias->nombreTipo($idTipoPreferencia);

            if ($nombre !== null) {
                $this->preferencias->insertarPreferenciaPersonal($idUsuario, $nombre, $idTipoPreferencia);
            }
        }
    }

    /**
     * @param array<int, array{idActividad: int, idTipoPreferencia: int}> $categoriasActividad
     * @param PreferenciaUsuario[] $preferenciasUsuario
     */
    private function actualizarPuntosInteres(array $categoriasActividad, array $preferenciasUsuario, string $estado): void
    {
        $puntos = $estado === 'aceptada' ? 1 : -1;

        foreach ($categoriasActividad as $categoria) {
            foreach ($preferenciasUsuario as $preferencia) {
                if ($preferencia->idTipoPreferencia !== $categoria['idTipoPreferencia']) {
                    continue;
                }

                $nuevoInteres = $preferencia->pInteres + $puntos;

                if ($nuevoInteres === 0) {
                    $this->preferencias->eliminarPreferenciaPersonal(
                        $preferencia->idUsuario,
                        $preferencia->nombreTipoPreferencia,
                        $preferencia->idTipoPreferencia,
                    );
                } else {
                    $this->preferencias->actualizarPuntoInteres($preferencia->idTipoPreferencia, $nuevoInteres);
                }
            }
        }
    }
}

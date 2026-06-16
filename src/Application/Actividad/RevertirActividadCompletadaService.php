<?php

declare(strict_types=1);

namespace App\Application\Actividad;

use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Domain\Preferencia\PreferenciaRepositoryInterface;
use App\Domain\Preferencia\PreferenciaUsuario;

/**
 * Marks a completed activity as pending again and adjusts the user's
 * preference scores based on whether they re-accepted or rejected it --
 * ported from BD::actualizarPuntosInteres() plus the surrounding logic in
 * the old volverArealizarActividad.php entry point.
 */
final readonly class RevertirActividadCompletadaService
{
    public function __construct(
        private ActividadRepositoryInterface $actividades,
        private PreferenciaRepositoryInterface $preferencias,
    ) {}

    public function ejecutar(int $idUsuario, int $idActividad, string $estado): bool
    {
        if ($estado !== 'aceptada') {
            return false;
        }

        $categoriasActividad = $this->actividades->categorias($idActividad);
        $preferenciasUsuario = $this->preferencias->preferenciasUsuario($idUsuario);

        if ($preferenciasUsuario !== null) {
            $this->actualizarPuntosInteres($categoriasActividad, $preferenciasUsuario, $estado);
        }

        $resultado = $this->actividades->descompletar($idUsuario, $idActividad);

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

        return $resultado;
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

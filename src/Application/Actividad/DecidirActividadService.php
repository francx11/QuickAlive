<?php

declare(strict_types=1);

namespace App\Application\Actividad;

use App\Domain\Actividad\ActividadRepositoryInterface;

/**
 * Ported from controladorInteresActividad.php: records the user's
 * accept/reject decision on a recommended activity and updates their
 * preference scores accordingly.
 */
final readonly class DecidirActividadService
{
    public function __construct(
        private ActividadRepositoryInterface $actividades,
        private ActualizarInteresActividadService $actualizarInteres,
    ) {}

    public function decidir(int $idUsuario, int $idActividad, string $estado): void
    {
        $this->actualizarInteres->actualizar($idUsuario, $idActividad, $estado);

        if ($estado === 'aceptada') {
            $this->actividades->marcarRealizada($idUsuario, $idActividad);
        } elseif ($estado === 'rechazada') {
            $this->actividades->rechazar($idUsuario, $idActividad);
        }
    }
}

<?php

declare(strict_types=1);

namespace App\Application\Actividad;

use App\Domain\Actividad\ActividadRepositoryInterface;

final readonly class RevertirActividadCompletadaService
{
    public function __construct(
        private ActividadRepositoryInterface $actividades,
        private ActualizarInteresActividadService $actualizarInteres,
    ) {}

    public function ejecutar(int $idUsuario, int $idActividad, string $estado): bool
    {
        if ($estado !== 'aceptada') {
            return false;
        }

        $this->actualizarInteres->actualizar($idUsuario, $idActividad, $estado);

        return $this->actividades->descompletar($idUsuario, $idActividad);
    }
}

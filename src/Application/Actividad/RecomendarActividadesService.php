<?php

declare(strict_types=1);

namespace App\Application\Actividad;

use App\Domain\Actividad\ActividadRepositoryInterface;
use App\Domain\Preferencia\PreferenciaRepositoryInterface;

/**
 * Ported from BD::recomendarActividadesPersonalizadas() / recomendarActividades().
 * Without preferences (or all scores at zero), every activity is returned.
 * Otherwise, activities not already accepted/rejected are scored by summing
 * the user's interest points for each matching category, highest first.
 */
final readonly class RecomendarActividadesService
{
    public function __construct(
        private ActividadRepositoryInterface $actividades,
        private PreferenciaRepositoryInterface $preferencias,
    ) {}

    /** @return array<int, array<string, mixed>> */
    public function paraUsuario(int $idUsuario): array
    {
        $preferenciasUsuario = $this->preferencias->preferenciasUsuario($idUsuario);
        $sinPreferencias = $preferenciasUsuario === null
            || array_sum(array_map(static fn ($p) => $p->pInteres, $preferenciasUsuario)) === 0;

        if ($sinPreferencias) {
            return array_map(
                fn (array $actividad): array => [...$actividad, 'fotos' => $this->urlsGaleria($actividad['idActividad'])],
                $this->actividades->todas(),
            );
        }

        $puntuaciones = [];

        foreach ($this->actividades->todasConCategorias() as $categoria) {
            $idActividad = $categoria['idActividad'];

            if ($this->actividades->estaEnRealiza($idActividad) || !$this->actividades->noEstaEnRechazadas($idActividad)) {
                continue;
            }

            $puntuaciones[$idActividad] ??= 0;

            foreach ($preferenciasUsuario as $preferenciaUsuario) {
                if ($preferenciaUsuario->idTipoPreferencia === $categoria['idTipoPreferencia']) {
                    $puntuaciones[$idActividad] += $preferenciaUsuario->pInteres;
                }
            }
        }

        arsort($puntuaciones);

        $recomendadas = [];

        foreach ($puntuaciones as $idActividad => $puntuacion) {
            if ($puntuacion < 0) {
                continue;
            }

            $actividad = $this->actividades->porId($idActividad);

            if ($actividad === null) {
                continue;
            }

            $recomendadas[] = [
                'idActividad' => $actividad->idActividad,
                'nombreActividad' => $actividad->nombreActividad,
                'descripcion' => $actividad->descripcion,
                'duracion' => $actividad->duracion,
                'tipoActividad' => $actividad->tipoActividad,
                'fotos' => $actividad->galeriaFotos,
            ];
        }

        return $recomendadas;
    }

    /** @return string[] */
    private function urlsGaleria(int $idActividad): array
    {
        return array_map(static fn ($imagen) => $imagen->url, $this->actividades->galeria($idActividad));
    }
}

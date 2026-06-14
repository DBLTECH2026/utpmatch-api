<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\Vacancy;

/**
 * MatchService — Motor de Match (perfil ↔ vacantes).
 *
 * % match = demanda de la vacante cubierta por las skills del alumno
 *           / demanda total de la vacante. Lista los faltantes.
 *
 * La "búsqueda conversacional" se simula filtrando por texto sobre
 * título/empresa/ubicación/modalidad (sin IA externa, datos demo).
 */
class MatchService
{
    /**
     * Devuelve vacantes con su % match y faltantes, ordenadas por match desc.
     *
     * @return array<int, array{vacancy: Vacancy, match_pct: int, faltantes: array}>
     */
    public function search(Profile $profile, ?string $query = null): array
    {
        $mias = $profile->skills()->pluck('skills.id')->all();

        $q = Vacancy::query()->with('skills');

        if ($query) {
            $q->where(function ($w) use ($query) {
                foreach (['titulo', 'empresa', 'ubicacion', 'modalidad', 'rol_objetivo'] as $col) {
                    $w->orWhere($col, 'like', "%{$query}%");
                }
            });
        }

        $resultados = [];
        foreach ($q->get() as $vac) {
            $total = 0;
            $cubierta = 0;
            $faltantes = [];

            foreach ($vac->skills as $skill) {
                $dem = $skill->pivot->demanda_pct;
                $total += $dem;
                if (in_array($skill->id, $mias, true)) {
                    $cubierta += $dem;
                } else {
                    $faltantes[] = $skill->nombre;
                }
            }

            $pct = $total > 0 ? (int) round(($cubierta / $total) * 100) : 0;

            $resultados[] = [
                'vacancy'   => $vac,
                'match_pct' => $pct,
                'faltantes' => $faltantes,
            ];
        }

        usort($resultados, fn ($a, $b) => $b['match_pct'] <=> $a['match_pct']);

        return $resultados;
    }

    /** Detalle de una vacante con match + requisitos cumplidos/faltantes. */
    public function detail(Profile $profile, Vacancy $vacancy): array
    {
        $vacancy->load('skills');
        $mias = $profile->skills()->pluck('skills.id')->all();

        $cumple = [];
        $falta = [];
        $total = 0;
        $cubierta = 0;

        foreach ($vacancy->skills as $skill) {
            $dem = $skill->pivot->demanda_pct;
            $total += $dem;
            if (in_array($skill->id, $mias, true)) {
                $cubierta += $dem;
                $cumple[] = $skill->nombre;
            } else {
                $falta[] = $skill->nombre;
            }
        }

        return [
            'vacancy'   => $vacancy,
            'match_pct' => $total > 0 ? (int) round(($cubierta / $total) * 100) : 0,
            'cumple'    => $cumple,
            'faltantes' => $falta,
        ];
    }
}

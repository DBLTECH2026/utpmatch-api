<?php

namespace App\Services;

use App\Support\CareerCatalog;
use Illuminate\Support\Str;

/**
 * RoleSuggestionService — Recomienda roles de la carrera del alumno según
 * cuánto coinciden sus fortalezas/intereses con la demanda de skills de cada
 * rol (datos del catálogo). Algoritmo simple y explicable: puntaje = suma de
 * demanda_pct de los skills que el alumno declara tener.
 */
class RoleSuggestionService
{
    /**
     * @param  list<string>  $tokens  fortalezas + intereses declarados
     * @return list<array{rol:string, puntaje:int, skills_coinciden:list<string>}>
     */
    public function suggest(?string $carrera, array $tokens): array
    {
        $roles = CareerCatalog::CARRERAS[$carrera] ?? [];
        if (empty($roles)) {
            return [];
        }

        $norm = static fn (string $s): string => Str::lower(Str::ascii(trim($s)));
        $tokenSet = array_map($norm, $tokens);

        $out = [];
        foreach ($roles as $rol => $skills) {
            $puntaje = 0;
            $coinciden = [];
            foreach ($skills as $skill => $pct) {
                if (in_array($norm($skill), $tokenSet, true)) {
                    $puntaje += $pct;
                    $coinciden[] = $skill;
                }
            }
            $out[] = ['rol' => $rol, 'puntaje' => $puntaje, 'skills_coinciden' => $coinciden];
        }

        usort($out, static fn ($a, $b) => $b['puntaje'] <=> $a['puntaje']);

        return $out;
    }
}

<?php

namespace App\Services;

use App\Models\Cv;
use App\Models\Profile;

/**
 * CvService — Genera el CV sobre plantilla estándar (RF-CV-02) y calcula ATS.
 *
 * NOTA: en producción la generación de contenido y el scoring ATS los hace
 * Claude API. Aquí se arma con datos del perfil y un scoring heurístico
 * (datos demo realistas), dejando el punto de integración claro.
 */
class CvService
{
    public function generate(Profile $profile, string $rol, string $plantilla = 'estandar'): Cv
    {
        $profile->loadMissing('user', 'skills');
        $user = $profile->user;

        $tecnicas = $profile->skills->where('categoria', 'tecnica')->pluck('nombre')->values()->all();
        $blandas  = $profile->skills->where('categoria', 'blanda')->pluck('nombre')->values()->all();

        // Estructura fija de la plantilla (espejo del prototipo / guía).
        $json = [
            'encabezado' => [
                'nombre'       => $user->name,
                'rol_objetivo' => $rol,
                'contacto'     => ['email' => $user->email],
                'links'        => ['linkedin' => '', 'github' => ''],
            ],
            'resumen'   => $profile->about ?: "Estudiante UTP de {$user->carrera}, ciclo {$user->ciclo}, en ruta a {$rol}.",
            'skills'    => ['tecnicas' => $tecnicas, 'blandas' => $blandas],
            'educacion' => [[
                'carrera'      => $user->carrera,
                'institucion'  => 'UTP',
                'ciclo'        => $user->ciclo,
            ]],
        ];

        [$score, $sugerencias] = $this->scoreAts($json);

        return $profile->cvs()->create([
            'rol_objetivo' => $rol,
            'plantilla'    => $plantilla,
            'json_data'    => $json,
            'ats_score'    => $score,
            'sugerencias'  => $sugerencias,
            'version'      => ($profile->cvs()->where('rol_objetivo', $rol)->max('version') ?? 0) + 1,
        ]);
    }

    /**
     * Scoring ATS heurístico (demo). Penaliza secciones vacías y pocas skills.
     *
     * @return array{0:int, 1:array<int,array{punto:string, taller:string}>}
     */
    private function scoreAts(array $json): array
    {
        $score = 60;
        $sug = [];

        if (! empty($json['resumen']) && mb_strlen($json['resumen']) > 40) $score += 8;
        $nTec = count($json['skills']['tecnicas'] ?? []);
        $score += min(20, $nTec * 4);
        if (! empty($json['skills']['blandas'])) $score += 6; else {
            $sug[] = ['punto' => 'Comunicación', 'taller' => 'Taller de Oratoria'];
        }
        if (empty($json['educacion'][0]['carrera'])) $score -= 5;

        // Sugerencias de talleres por puntos débiles (demo).
        $sug[] = ['punto' => 'Inglés técnico', 'taller' => 'Taller de Inglés UTP'];

        return [max(0, min(100, $score)), $sug];
    }
}

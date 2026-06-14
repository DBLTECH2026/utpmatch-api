<?php

namespace App\Services;

use App\Models\CopilotNudge;
use App\Models\User;

/**
 * CopilotService — Copiloto proactivo (nudges) + chat.
 *
 * NOTA: en producción el chat conversacional lo responde Claude API con el
 * contexto del alumno. Aquí devolvemos respuestas demo basadas en reglas
 * simples sobre el estado del perfil (sin key, datos realistas).
 */
class CopilotService
{
    /** Nudges no leídos del usuario. */
    public function nudges(User $user)
    {
        return $user->nudges()->where('leido', false)->latest()->get();
    }

    public function marcarLeido(User $user, int $nudgeId): void
    {
        $user->nudges()->whereKey($nudgeId)->update(['leido' => true]);
    }

    /**
     * Responde un mensaje del alumno (demo basado en reglas).
     */
    public function chat(User $user, string $mensaje): string
    {
        $m = mb_strtolower($mensaje);
        $profile = $user->profile;
        $rol = $profile?->rol_objetivo ?? 'tu meta';

        if (str_contains($m, 'cv')) {
            $ats = $profile?->cvs()->latest()->value('ats_score') ?? 0;
            return "Tu CV está en ATS {$ats}. Si tomas el taller de Oratoria sube unos puntos. ¿Lo agrego a tu ruta?";
        }
        if (str_contains($m, 'ruta') || str_contains($m, 'falta') || str_contains($m, 'brecha')) {
            return "Para {$rol} prioriza React (88%) → TypeScript → CSS avanzado. Empieza por el taller UTP de React.";
        }
        if (str_contains($m, 'empleo') || str_contains($m, 'vacante') || str_contains($m, 'trabajo')) {
            return "Tienes 12 vacantes compatibles. La de mayor match es Practicante Frontend en Culqi (78%). ¿La revisamos?";
        }

        return "Estoy para ayudarte con tu ruta, tu CV y tus matches. ¿Qué quieres avanzar hoy hacia {$rol}?";
    }
}

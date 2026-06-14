<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\GapRouteService;
use App\Services\MatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * DashboardController — Resumen del "viaje" del alumno.
 * GET /dashboard → score, match actual→meta, brechas, vacantes, ruta, nudge.
 * Agrega datos de varios services (composición en la capa de presentación).
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly GapRouteService $engine,
        private readonly MatchService $matcher,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->profile()->firstOrCreate([]);

        // Sin meta definida → estado vacío (el front muestra CTA "Define tu meta").
        if (! $profile->rol_objetivo) {
            return response()->json(['data' => [
                'sin_meta' => true,
                'usuario'  => $user->name,
                'score'    => $profile->empleabilidad_score,
            ]]);
        }

        $rol = $profile->rol_objetivo;

        $route = $this->engine->buildRoute($profile, $rol);
        $matches = $this->matcher->search($profile, null);
        $nudge = $user->nudges()->where('leido', false)->latest()->first();

        return response()->json([
            'data' => [
                'usuario'      => $user->name,
                'rol_objetivo' => $rol,
                'score'        => $profile->empleabilidad_score,
                'match_actual' => $route->match_actual,
                'match_meta'   => $route->match_meta,
                'brechas'      => $route->items->count(),
                'vacantes'     => count($matches),
                'ruta'         => $route->items->map(fn ($it) => [
                    'orden'  => $it->orden,
                    'skill'  => $it->skill->nombre,
                    'estado' => $it->estado,
                    'taller' => $it->taller?->nombre,
                    'demanda_pct' => $it->demanda_pct,
                ]),
                'nudge' => $nudge ? [
                    'mensaje'   => $nudge->mensaje,
                    'cta_label' => $nudge->cta_label,
                    'cta_route' => $nudge->cta_route,
                ] : null,
            ],
        ]);
    }
}

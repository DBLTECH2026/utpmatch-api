<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\GapRouteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * RouteController — Ruta & Brechas (gap engine).
 * GET /route?rol=...     → brechas + ruta priorizada hacia el rol
 * PUT /route/target      → cambia el rol objetivo y RECALCULA todo
 * GET /gaps?rol=...      → solo "ya tienes / te falta"
 */
class RouteController extends Controller
{
    public function __construct(private readonly GapRouteService $engine) {}

    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->profile()->firstOrCreate([]);
        $rol = $request->query('rol') ?: $profile->rol_objetivo;

        if (! $rol) {
            return response()->json(['message' => 'Define un rol objetivo primero.'], 422);
        }

        $route = $this->engine->buildRoute($profile, $rol);

        return response()->json(['data' => $this->serializeRoute($route)]);
    }

    public function gaps(Request $request): JsonResponse
    {
        $profile = $request->user()->profile()->firstOrCreate([]);
        $rol = $request->query('rol') ?: $profile->rol_objetivo;

        if (! $rol) {
            return response()->json(['message' => 'Define un rol objetivo primero.'], 422);
        }

        return response()->json(['data' => $this->engine->computeGaps($profile, $rol)]);
    }

    public function setTarget(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rol_objetivo' => ['required', 'string', 'max:120'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        $profile->update(['rol_objetivo' => $data['rol_objetivo']]);

        // Cambiar la meta RECALCULA ruta + brechas (regla de negocio clave).
        $route = $this->engine->buildRoute($profile, $data['rol_objetivo']);

        return response()->json([
            'message' => 'Meta actualizada. Ruta recalculada.',
            'data'    => $this->serializeRoute($route),
        ]);
    }

    private function serializeRoute(\App\Models\Route $route): array
    {
        return [
            'rol_objetivo' => $route->rol_objetivo,
            'match_actual' => $route->match_actual,
            'match_meta'   => $route->match_meta,
            'pasos'        => $route->items->map(fn ($it) => [
                'orden'       => $it->orden,
                'skill'       => $it->skill->nombre,
                'demanda_pct' => $it->demanda_pct,
                'estado'      => $it->estado,
                'taller'      => $it->taller?->nombre,
            ]),
        ];
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AdvisorController — Asesores externos.
 * GET  /advisors           → lista de asesores
 * POST /advisor-sessions   → solicita una sesión con un asesor
 */
class AdvisorController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Advisor::orderByDesc('rating')->get()->map(fn ($a) => [
                'id'           => $a->id,
                'nombre'       => $a->nombre,
                'especialidad' => $a->especialidad,
                'empresa'      => $a->empresa,
                'rating'       => $a->rating,
            ]),
        ]);
    }

    public function requestSession(Request $request): JsonResponse
    {
        $data = $request->validate([
            'advisor_id' => ['required', 'integer', 'exists:advisors,id'],
        ]);

        $session = $request->user()->advisorSessions()->create([
            'advisor_id' => $data['advisor_id'],
            'estado'     => 'solicitada',
        ]);

        return response()->json([
            'message' => 'Sesión solicitada.',
            'data'    => ['id' => $session->id, 'estado' => $session->estado],
        ], 201);
    }
}

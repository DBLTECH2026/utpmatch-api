<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Cv;
use App\Services\CvService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CvController — CV Inteligente.
 * POST /cv            → genera CV sobre plantilla (rol, plantilla)
 * GET  /cv/{cv}       → json_data del CV
 * GET  /cv/{cv}/ats   → score ATS + sugerencias (talleres)
 */
class CvController extends Controller
{
    public function __construct(private readonly CvService $cvService) {}

    public function generate(Request $request): JsonResponse
    {
        $data = $request->validate([
            'rol'       => ['nullable', 'string', 'max:120'],
            'plantilla' => ['nullable', 'string', 'max:60'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        $rol = $data['rol'] ?? $profile->rol_objetivo ?? 'Frontend Jr.';

        $cv = $this->cvService->generate($profile, $rol, $data['plantilla'] ?? 'estandar');

        return response()->json([
            'message' => 'CV generado.',
            'data'    => $this->serialize($cv),
        ], 201);
    }

    public function show(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeOwner($request, $cv);
        return response()->json(['data' => $this->serialize($cv)]);
    }

    public function ats(Request $request, Cv $cv): JsonResponse
    {
        $this->authorizeOwner($request, $cv);
        return response()->json([
            'data' => ['ats_score' => $cv->ats_score, 'sugerencias' => $cv->sugerencias],
        ]);
    }

    /** Garantiza que el CV pertenece al usuario (anti-IDOR, A01 OWASP). */
    private function authorizeOwner(Request $request, Cv $cv): void
    {
        abort_unless($cv->profile->user_id === $request->user()->id, 403, 'No autorizado.');
    }

    private function serialize(Cv $cv): array
    {
        return [
            'id'           => $cv->id,
            'rol_objetivo' => $cv->rol_objetivo,
            'plantilla'    => $cv->plantilla,
            'json_data'    => $cv->json_data,
            'ats_score'    => $cv->ats_score,
            'sugerencias'  => $cv->sugerencias,
            'version'      => $cv->version,
        ];
    }
}

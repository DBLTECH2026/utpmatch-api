<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\CopilotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * CopilotController — Copiloto IA proactivo.
 * GET  /copilot/nudges  → avisos no leídos
 * POST /copilot/chat    → mensaje conversacional (demo basado en reglas)
 */
class CopilotController extends Controller
{
    public function __construct(private readonly CopilotService $copilot) {}

    public function nudges(Request $request): JsonResponse
    {
        $nudges = $this->copilot->nudges($request->user());

        return response()->json([
            'data' => $nudges->map(fn ($n) => [
                'id'        => $n->id,
                'tipo'      => $n->tipo,
                'mensaje'   => $n->mensaje,
                'cta_label' => $n->cta_label,
                'cta_route' => $n->cta_route,
            ]),
        ]);
    }

    public function chat(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mensaje' => ['required', 'string', 'max:500'],
        ]);

        $respuesta = $this->copilot->chat($request->user(), $data['mensaje']);

        return response()->json(['data' => ['respuesta' => $respuesta]]);
    }
}

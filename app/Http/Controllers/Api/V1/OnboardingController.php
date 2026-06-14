<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\RoleSuggestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * OnboardingController — Sugerencia de rol para el wizard de onboarding.
 * POST /onboarding/suggest → roles de la carrera del usuario, puntuados por
 * overlap con sus fortalezas/intereses.
 */
class OnboardingController extends Controller
{
    public function suggest(Request $request, RoleSuggestionService $suggester): JsonResponse
    {
        $data = $request->validate([
            'autopercepcion'   => ['sometimes', 'array'],
            'autopercepcion.*' => ['string', 'max:80'],
            'intereses'        => ['sometimes', 'array'],
            'intereses.*'      => ['string', 'max:80'],
            'fortalezas'       => ['sometimes', 'array'],
            'fortalezas.*'     => ['string', 'max:80'],
        ]);

        $tokens = array_merge($data['fortalezas'] ?? [], $data['intereses'] ?? []);

        return response()->json([
            'data' => $suggester->suggest($request->user()->carrera, $tokens),
        ]);
    }
}

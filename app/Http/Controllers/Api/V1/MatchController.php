<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use App\Services\MatchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * MatchController — Empleos & Match.
 * POST /match/search       → búsqueda conversacional → vacantes con % match
 * GET  /match/{vacancy}    → detalle + "qué te falta"
 */
class MatchController extends Controller
{
    public function __construct(private readonly MatchService $matcher) {}

    public function search(Request $request): JsonResponse
    {
        $data = $request->validate([
            'query' => ['nullable', 'string', 'max:200'],
        ]);

        $profile = $request->user()->profile()->firstOrCreate([]);
        $results = $this->matcher->search($profile, $data['query'] ?? null);

        return response()->json([
            'data' => array_map(fn ($r) => [
                'id'        => $r['vacancy']->id,
                'titulo'    => $r['vacancy']->titulo,
                'empresa'   => $r['vacancy']->empresa,
                'ubicacion' => $r['vacancy']->ubicacion,
                'modalidad' => $r['vacancy']->modalidad,
                'match_pct' => $r['match_pct'],
                'faltantes' => $r['faltantes'],
            ], $results),
        ]);
    }

    public function detail(Request $request, Vacancy $vacancy): JsonResponse
    {
        $profile = $request->user()->profile()->firstOrCreate([]);
        $d = $this->matcher->detail($profile, $vacancy);

        return response()->json([
            'data' => [
                'id'        => $vacancy->id,
                'titulo'    => $vacancy->titulo,
                'empresa'   => $vacancy->empresa,
                'ubicacion' => $vacancy->ubicacion,
                'modalidad' => $vacancy->modalidad,
                'salario'   => $vacancy->salario,
                'match_pct' => $d['match_pct'],
                'cumple'    => $d['cumple'],
                'faltantes' => $d['faltantes'],
            ],
        ]);
    }
}

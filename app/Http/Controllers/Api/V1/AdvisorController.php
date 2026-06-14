<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Advisor;
use App\Models\AdvisorSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AdvisorController
 *
 * Rutas de alumno:
 *   GET  /advisors           → lista pública de asesores
 *   POST /advisor-sessions   → solicitar sesión
 *
 * Rutas de asesor (role:asesor):
 *   GET  /advisor/dashboard              → resumen del asesor
 *   GET  /advisor/reservas               → todas sus sesiones
 *   PUT  /advisor/sessions/{id}/schedule → agendar fecha + notas
 *   PUT  /advisor/sessions/{id}/status   → cambiar estado
 */
class AdvisorController extends Controller
{
    // ── Alumno ──────────────────────────────────────────────────────────────

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Advisor::orderByDesc('rating')->get()->map(fn ($a) => [
                'id'           => $a->id,
                'nombre'       => $a->nombre,
                'especialidad' => $a->especialidad,
                'empresa'      => $a->empresa,
                'rating'       => $a->rating,
                'bio'          => $a->bio,
                'linkedin_url' => $a->linkedin_url,
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

    // ── Alumno: ver sus propias sesiones ────────────────────────────────────

    public function mySessions(Request $request): JsonResponse
    {
        $sessions = $request->user()->advisorSessions()
            ->with('advisor')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($s) => [
                'id'          => $s->id,
                'advisor_id'  => $s->advisor_id,
                'asesor'      => $s->advisor->nombre,
                'especialidad'=> $s->advisor->especialidad,
                'estado'      => $s->estado,
                'fecha'       => $s->fecha?->toIso8601String(),
                'notas'       => $s->notas,
                'zoom_link'   => $s->zoom_link,
            ]);

        return response()->json(['data' => $sessions]);
    }

    // ── Asesor ──────────────────────────────────────────────────────────────

    public function dashboard(Request $request): JsonResponse
    {
        $advisor = Advisor::where('user_id', $request->user()->id)->firstOrFail();
        $sessions = $advisor->sessions()->with('user')->get();

        $hoy = now()->startOfDay();
        $finDia = now()->endOfDay();

        return response()->json([
            'data' => [
                'nombre'      => $advisor->nombre,
                'especialidad'=> $advisor->especialidad,
                'empresa'     => $advisor->empresa,
                'rating'      => $advisor->rating,
                'pendientes'  => $sessions->where('estado', 'solicitada')->count(),
                'confirmadas' => $sessions->where('estado', 'confirmada')->count(),
                'completadas' => $sessions->where('estado', 'completada')->count(),
                'hoy'         => $sessions->filter(fn ($s) =>
                    $s->estado === 'confirmada' && $s->fecha &&
                    $s->fecha->between($hoy, $finDia)
                )->map(fn ($s) => $this->sessionResource($s))->values(),
                'proximas'    => $sessions->filter(fn ($s) =>
                    $s->estado === 'confirmada' && $s->fecha && $s->fecha->gt($finDia)
                )->sortBy('fecha')->take(5)->map(fn ($s) => $this->sessionResource($s))->values(),
            ],
        ]);
    }

    public function reservas(Request $request): JsonResponse
    {
        $advisor = Advisor::where('user_id', $request->user()->id)->firstOrFail();

        $sessions = $advisor->sessions()
            ->with('user')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($s) => $this->sessionResource($s));

        return response()->json(['data' => $sessions]);
    }

    public function schedule(Request $request, AdvisorSession $session): JsonResponse
    {
        $advisor = Advisor::where('user_id', $request->user()->id)->firstOrFail();

        if ($session->advisor_id !== $advisor->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'fecha'     => ['required', 'date', 'after:yesterday'],
            'notas'     => ['nullable', 'string', 'max:500'],
            'zoom_link' => ['nullable', 'url', 'max:500'],
        ]);

        $session->update([
            'fecha'     => $data['fecha'],
            'notas'     => $data['notas'] ?? null,
            'zoom_link' => $data['zoom_link'] ?? null,
            'estado'    => 'confirmada',
        ]);

        return response()->json([
            'message' => 'Sesión agendada.',
            'data'    => $this->sessionResource($session->fresh(['user'])),
        ]);
    }

    public function updateStatus(Request $request, AdvisorSession $session): JsonResponse
    {
        $advisor = Advisor::where('user_id', $request->user()->id)->firstOrFail();

        if ($session->advisor_id !== $advisor->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $data = $request->validate([
            'estado' => ['required', 'in:confirmada,completada,cancelada'],
        ]);

        $session->update(['estado' => $data['estado']]);

        return response()->json([
            'message' => 'Estado actualizado.',
            'data'    => ['id' => $session->id, 'estado' => $session->estado],
        ]);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    private function sessionResource(AdvisorSession $s): array
    {
        return [
            'id'         => $s->id,
            'usuario'    => $s->user->name,
            'email'      => $s->user->email,
            'carrera'    => $s->user->carrera,
            'ciclo'      => $s->user->ciclo,
            'estado'     => $s->estado,
            'fecha'      => $s->fecha?->toIso8601String(),
            'notas'      => $s->notas,
            'zoom_link'  => $s->zoom_link,
            'created_at' => $s->created_at->toIso8601String(),
        ];
    }
}

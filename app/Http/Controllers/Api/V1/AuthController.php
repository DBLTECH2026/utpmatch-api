<?php

namespace App\Http\Controllers\Api\V1;

use App\DataTransferObjects\RegisterUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AuthController — Capa de Presentación (HTTP) del módulo de autenticación.
 *
 * Responsabilidad ÚNICA (SRP): traducir HTTP ↔ dominio.
 *  1. Recibe el FormRequest YA validado.
 *  2. Construye el DTO y delega TODO al Service (sin lógica de negocio aquí).
 *  3. Devuelve la respuesta serializada con un Resource.
 *
 * Contrato de respuesta uniforme: { data, message }.
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $auth,
    ) {}

    /** POST /api/v1/auth/register */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->auth->register(
            RegisterUserData::fromArray($request->validated())
        );

        return response()->json([
            'message' => 'Cuenta creada correctamente.',
            'data'    => [
                'user'  => UserResource::make($result['user']),
                'token' => $result['token'],
            ],
        ], 201);
    }

    /** POST /api/v1/auth/login */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->auth->login(
            $request->validated('email'),
            $request->validated('password'),
        );

        return response()->json([
            'message' => 'Autenticación exitosa.',
            'data'    => [
                'user'  => UserResource::make($result['user']->load('profile.skills', 'courses', 'connections')),
                'token' => $result['token'],
            ],
        ]);
    }

    /** POST /api/v1/auth/logout (requiere token) */
    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    /** GET /api/v1/me — usuario autenticado actual */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['profile.skills', 'connections', 'courses']);

        return response()->json([
            'data' => UserResource::make($user),
        ]);
    }
}

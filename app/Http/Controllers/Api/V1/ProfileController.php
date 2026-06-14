<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * ProfileController — HTTP del Perfil 360.
 *
 * Siempre opera sobre $request->user() (el dueño autenticado), por lo que
 * no expone IDs en la URL → previene IDOR (OWASP A01).
 */
class ProfileController extends Controller
{
    public function __construct(
        private readonly ProfileService $profiles,
    ) {}

    /** GET /api/v1/profile — perfil 360 del usuario actual */
    public function show(Request $request): JsonResponse
    {
        $profile = $this->profiles->getForUser($request->user());

        return response()->json([
            'data' => ProfileResource::make($profile),
        ]);
    }

    /** PUT /api/v1/profile — actualiza el perfil del usuario actual */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $profile = $this->profiles->update(
            $request->user(),
            $request->validated(),
        );

        return response()->json([
            'message' => 'Perfil actualizado.',
            'data'    => ProfileResource::make($profile),
        ]);
    }
}

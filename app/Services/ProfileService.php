<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\User;
use App\Repositories\Contracts\ProfileRepositoryInterface;

/**
 * ProfileService — Lógica de negocio del Perfil 360.
 *
 * Capa: Service. Siempre opera sobre el usuario AUTENTICADO que le pasa el
 * controller, de modo que un usuario nunca puede tocar el perfil de otro
 * (mitiga IDOR / Broken Object Level Authorization, OWASP A01).
 */
class ProfileService
{
    public function __construct(
        private readonly ProfileRepositoryInterface $profiles,
    ) {}

    /** Devuelve el Perfil 360 completo del usuario (con skills y conexiones). */
    public function getForUser(User $user): ?Profile
    {
        return $this->profiles->getFullProfile($user);
    }

    /**
     * Actualiza campos editables del perfil del usuario autenticado.
     *
     * @param array<string,mixed> $data Ya validado por UpdateProfileRequest.
     */
    public function update(User $user, array $data): Profile
    {
        // Garantiza que el perfil exista (defensa ante estados inconsistentes).
        $profile = $user->profile()->firstOrCreate([]);

        // Solo se persisten claves whitelisteadas por el FormRequest.
        return $this->profiles->update($profile, $data);
    }
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Profile;
use App\Models\User;

/**
 * ProfileRepositoryInterface — Contrato del DAO de perfiles.
 */
interface ProfileRepositoryInterface
{
    /** Perfil 360 con relaciones (skills, conexiones) cargadas. */
    public function getFullProfile(User $user): ?Profile;

    /** @param array<string,mixed> $data */
    public function createForUser(User $user, array $data): Profile;

    /** @param array<string,mixed> $data */
    public function update(Profile $profile, array $data): Profile;
}

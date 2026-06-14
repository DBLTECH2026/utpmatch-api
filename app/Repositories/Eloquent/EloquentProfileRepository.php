<?php

namespace App\Repositories\Eloquent;

use App\Models\Profile;
use App\Models\User;
use App\Repositories\Contracts\ProfileRepositoryInterface;

/**
 * EloquentProfileRepository — DAO de perfiles sobre Eloquent.
 */
class EloquentProfileRepository implements ProfileRepositoryInterface
{
    public function getFullProfile(User $user): ?Profile
    {
        // Eager loading para evitar el problema N+1 (rendimiento).
        return $user->profile()
            ->with(['skills', 'user.connections'])
            ->first();
    }

    public function createForUser(User $user, array $data): Profile
    {
        return $user->profile()->create($data);
    }

    public function update(Profile $profile, array $data): Profile
    {
        $profile->update($data);

        return $profile->refresh();
    }
}

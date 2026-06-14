<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

/**
 * EloquentUserRepository — Implementación del DAO de usuarios sobre Eloquent.
 *
 * Capa: Repository (acceso a datos). Es la ÚNICA capa que conoce Eloquent
 * para usuarios; el resto del sistema usa la interfaz.
 *
 * Seguridad: las consultas usan Query Builder/Eloquent → binding parametrizado
 * automático (previene SQL Injection, OWASP A03).
 */
class EloquentUserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        return User::find($id);
    }

    public function findByEmail(string $email): ?User
    {
        // El binding evita inyección aunque $email venga del cliente.
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user->refresh();
    }
}

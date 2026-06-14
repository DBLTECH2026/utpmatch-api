<?php

namespace App\Repositories\Contracts;

use App\Models\User;

/**
 * UserRepositoryInterface — Contrato del DAO de usuarios.
 *
 * Patrón: Repository / DAO (Data Access Object) + Dependency Inversion (SOLID-D).
 * Los Services dependen de ESTA interfaz, no de Eloquent. Eso permite:
 *  - Cambiar la fuente de datos (Eloquent → otra) sin tocar la lógica.
 *  - Testear con un doble (mock) del repositorio.
 */
interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(string $email): ?User;

    /** @param array<string,mixed> $data */
    public function create(array $data): User;

    /** @param array<string,mixed> $data */
    public function update(User $user, array $data): User;
}

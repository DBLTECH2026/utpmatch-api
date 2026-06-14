<?php

namespace App\Services;

use App\DataTransferObjects\RegisterUserData;
use App\Models\User;
use App\Repositories\Contracts\ProfileRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * AuthService — Lógica de negocio de autenticación.
 *
 * Capa: Service (Business Logic). Orquesta repositorios y reglas de dominio.
 * NO conoce HTTP (ni Request ni Response): recibe DTOs/valores y devuelve modelos.
 * Las dependencias entran por constructor (Inyección de Dependencias).
 *
 * Seguridad:
 *  - Mensaje de error genérico en login (no revela si el email existe) → A07.
 *  - Hashing vía Hash::check / cast 'hashed' → A02.
 *  - Auditoría de eventos de auth (login OK/KO) → A09 Logging.
 *  - Transacción al registrar (atomicidad usuario + perfil).
 */
class AuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
        private readonly ProfileRepositoryInterface $profiles,
    ) {}

    /**
     * Registra un usuario + su perfil 360 vacío, en una transacción.
     * Devuelve el usuario y un token Bearer recién emitido.
     *
     * @return array{user: User, token: string}
     */
    public function register(RegisterUserData $data): array
    {
        $user = DB::transaction(function () use ($data) {
            $user = $this->users->create($data->toUserArray());

            // Todo alumno arranca con un perfil 360 vacío (invariante de dominio).
            $this->profiles->createForUser($user, [
                'rol_objetivo'        => null,
                'empleabilidad_score' => 0,
            ]);

            return $user;
        });

        Log::channel('stack')->info('auth.register', ['user_id' => $user->id]);

        return [
            'user'  => $user,
            'token' => $this->issueToken($user),
        ];
    }

    /**
     * Autentica por email + password. Lanza ValidationException si falla.
     *
     * @return array{user: User, token: string}
     */
    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        // Comparación de hash incluso si el usuario no existe sería ideal para
        // mitigar timing attacks; aquí mantenemos mensaje genérico (anti-enumeración).
        if (! $user || ! Hash::check($password, $user->password)) {
            Log::warning('auth.login.failed', ['email' => $email]);

            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son válidas.'],
            ]);
        }

        Log::info('auth.login.success', ['user_id' => $user->id]);

        return [
            'user'  => $user,
            'token' => $this->issueToken($user),
        ];
    }

    /**
     * Cierra sesión revocando el token actual (no todos los dispositivos).
     */
    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
        Log::info('auth.logout', ['user_id' => $user->id]);
    }

    /**
     * Emite un token Sanctum con expiración configurable y abilities por rol.
     */
    private function issueToken(User $user): string
    {
        // Abilities (scopes) mínimos según rol → privilegio mínimo (A01).
        $abilities = match ($user->rol) {
            'admin'  => ['*'],
            'asesor' => ['profile:read', 'advisor:manage'],
            default  => ['profile:read', 'profile:write'],
        };

        $expiresAt = now()->addMinutes((int) config('sanctum.expiration', 1440));

        return $user->createToken('api-token', $abilities, $expiresAt)->plainTextToken;
    }
}

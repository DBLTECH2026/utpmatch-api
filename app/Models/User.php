<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * User — Modelo de identidad y credenciales.
 *
 * Capa: Model (Eloquent / Active Record) en la arquitectura MVC.
 * Seguridad:
 *  - `password` se castea a 'hashed' → bcrypt automático (A02/A07).
 *  - `$hidden` impide exponer credenciales en serialización (A01).
 *  - HasApiTokens habilita tokens Bearer de Sanctum.
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    /**
     * Atributos asignables masivamente.
     * Lista BLANCA explícita → evita mass-assignment (A08 OWASP).
     * `rol` NO está aquí a propósito: no se puede elevar privilegios por request.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'codigo_utp',
        'carrera',
        'ciclo',
    ];

    /**
     * Atributos ocultos en arrays/JSON (nunca salen al cliente).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting de atributos.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // bcrypt al asignar
            'ciclo' => 'integer',
        ];
    }

    // ---------------------------------------------------------------------
    // Relaciones
    // ---------------------------------------------------------------------

    /** Perfil 360 (1:1). */
    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /** Conexiones externas (LinkedIn, GitHub…). */
    public function connections(): HasMany
    {
        return $this->hasMany(Connection::class);
    }

    /** Cursos (data UTP). */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /** Nudges del copiloto. */
    public function nudges(): HasMany
    {
        return $this->hasMany(CopilotNudge::class);
    }

    /** Sesiones con asesores. */
    public function advisorSessions(): HasMany
    {
        return $this->hasMany(AdvisorSession::class);
    }

    // ---------------------------------------------------------------------
    // Helpers de dominio
    // ---------------------------------------------------------------------

    /** ¿El usuario tiene alguno de los roles dados? (RBAC). */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->rol, $roles, true);
    }
}

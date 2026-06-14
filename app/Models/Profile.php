<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Profile — Perfil 360 de empleabilidad (1:1 con User).
 */
class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'rol_objetivo',
        'headline',
        'about',
        'empleabilidad_score',
    ];

    protected function casts(): array
    {
        return [
            'empleabilidad_score' => 'integer',
        ];
    }

    /** Usuario dueño del perfil. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Skills del perfil (N:M) con metadatos del pivote.
     * withPivot expone nivel/origen/verificado para trazabilidad.
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'profile_skills')
            ->withPivot(['nivel', 'origen', 'verificado'])
            ->withTimestamps();
    }
}

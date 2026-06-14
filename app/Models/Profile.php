<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'autopercepcion',
        'intereses',
        'fortalezas',
        'onboarding_visto',
    ];

    protected function casts(): array
    {
        return [
            'empleabilidad_score' => 'integer',
            'autopercepcion'      => 'array',
            'intereses'           => 'array',
            'fortalezas'          => 'array',
            'onboarding_visto'    => 'boolean',
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

    public function gaps(): HasMany
    {
        return $this->hasMany(Gap::class);
    }

    public function routes(): HasMany
    {
        return $this->hasMany(Route::class);
    }

    public function cvs(): HasMany
    {
        return $this->hasMany(Cv::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(VacancyMatch::class);
    }
}

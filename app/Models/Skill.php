<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Skill — Catálogo de habilidades (técnicas / blandas).
 */
class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'categoria',
    ];

    /** Perfiles que tienen esta skill (N:M). */
    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'profile_skills')
            ->withPivot(['nivel', 'origen', 'verificado'])
            ->withTimestamps();
    }
}

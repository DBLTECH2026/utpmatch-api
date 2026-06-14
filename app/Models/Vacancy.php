<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** Vacancy — Vacante con sus skills requeridas. */
class Vacancy extends Model
{
    protected $fillable = [
        'source', 'external_id', 'titulo', 'empresa', 'ubicacion',
        'modalidad', 'rol_objetivo', 'salario', 'descripcion_raw', 'url', 'fetched_at',
    ];

    protected function casts(): array
    {
        return ['fetched_at' => 'datetime'];
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'vacancy_skills')
            ->withPivot('demanda_pct')
            ->withTimestamps();
    }
}

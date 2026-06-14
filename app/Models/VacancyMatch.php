<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * VacancyMatch — Match perfil ↔ vacante (tabla `matches`).
 * Se llama VacancyMatch porque `Match` es palabra reservada en PHP 8.
 */
class VacancyMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = ['profile_id', 'vacancy_id', 'match_pct', 'faltantes_json'];

    protected function casts(): array
    {
        return ['match_pct' => 'integer', 'faltantes_json' => 'array'];
    }

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(Vacancy::class);
    }
}

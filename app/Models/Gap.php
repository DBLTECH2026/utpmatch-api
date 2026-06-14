<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Gap — Brecha de skill del alumno para un rol (tiene/falta). */
class Gap extends Model
{
    protected $fillable = ['profile_id', 'rol_objetivo', 'skill_id', 'estado', 'demanda_pct'];

    protected function casts(): array
    {
        return ['demanda_pct' => 'integer'];
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}

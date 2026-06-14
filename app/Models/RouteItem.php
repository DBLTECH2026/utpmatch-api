<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** RouteItem — Paso de una ruta (skill + taller). */
class RouteItem extends Model
{
    protected $fillable = ['route_id', 'skill_id', 'orden', 'taller_id', 'estado', 'demanda_pct'];

    protected function casts(): array
    {
        return ['orden' => 'integer', 'demanda_pct' => 'integer'];
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }

    public function taller(): BelongsTo
    {
        return $this->belongsTo(Taller::class);
    }
}

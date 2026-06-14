<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Taller — Taller UTP que cierra una skill. */
class Taller extends Model
{
    protected $table = 'talleres';

    protected $fillable = ['nombre', 'area', 'skill_id', 'url'];

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}

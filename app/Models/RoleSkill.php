<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** RoleSkill — Demanda de una skill para un rol objetivo. */
class RoleSkill extends Model
{
    protected $fillable = ['rol_objetivo', 'skill_id', 'demanda_pct'];

    protected function casts(): array
    {
        return ['demanda_pct' => 'integer'];
    }

    public function skill(): BelongsTo
    {
        return $this->belongsTo(Skill::class);
    }
}

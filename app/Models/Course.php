<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Course — Curso del alumno (data UTP). */
class Course extends Model
{
    protected $fillable = ['user_id', 'nombre', 'estado', 'nota', 'ciclo'];

    protected function casts(): array
    {
        return ['nota' => 'float', 'ciclo' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

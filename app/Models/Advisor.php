<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Advisor — Asesor/mentor externo. */
class Advisor extends Model
{
    protected $fillable = ['nombre', 'especialidad', 'empresa', 'rating', 'contacto'];

    protected function casts(): array
    {
        return ['rating' => 'float'];
    }
}

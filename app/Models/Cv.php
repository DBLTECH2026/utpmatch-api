<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Cv — CV generado (json_data) con score ATS. */
class Cv extends Model
{
    protected $fillable = [
        'profile_id', 'rol_objetivo', 'plantilla',
        'json_data', 'ats_score', 'sugerencias', 'version',
    ];

    protected function casts(): array
    {
        return [
            'json_data'   => 'array',
            'sugerencias' => 'array',
            'ats_score'   => 'integer',
            'version'     => 'integer',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}

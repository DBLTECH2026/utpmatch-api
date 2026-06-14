<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** AdvisorSession — Solicitud / sesión de asesoría. */
class AdvisorSession extends Model
{
    protected $fillable = ['user_id', 'advisor_id', 'estado', 'fecha', 'notas', 'zoom_link'];

    protected function casts(): array
    {
        return ['fecha' => 'datetime'];
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(Advisor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

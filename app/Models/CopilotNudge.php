<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** CopilotNudge — Aviso proactivo del copiloto. */
class CopilotNudge extends Model
{
    protected $fillable = ['user_id', 'tipo', 'mensaje', 'cta_label', 'cta_route', 'leido'];

    protected function casts(): array
    {
        return ['leido' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

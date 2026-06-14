<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Advisor — Asesor/mentor externo. */
class Advisor extends Model
{
    protected $fillable = ['user_id', 'nombre', 'especialidad', 'empresa', 'rating', 'contacto', 'bio', 'linkedin_url'];

    protected function casts(): array
    {
        return ['rating' => 'float'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(AdvisorSession::class);
    }
}

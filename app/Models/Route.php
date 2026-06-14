<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Route — Ruta de aprendizaje hacia un rol. */
class Route extends Model
{
    protected $fillable = ['profile_id', 'rol_objetivo', 'match_actual', 'match_meta'];

    protected function casts(): array
    {
        return ['match_actual' => 'integer', 'match_meta' => 'integer'];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(RouteItem::class)->orderBy('orden');
    }
}

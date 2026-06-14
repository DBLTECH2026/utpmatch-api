<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Connection — Cuenta externa vinculada (OAuth).
 *
 * SEGURIDAD (OWASP A02 Cryptographic Failures):
 *  - `access_token_enc` usa el cast 'encrypted': Laravel cifra al guardar
 *    (AES-256-CBC con APP_KEY) y descifra al leer. En BD queda ilegible.
 *  - `$hidden` evita que el token salga jamás en JSON.
 */
class Connection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'external_id',
        'access_token_enc',
        'last_sync_at',
        'status',
    ];

    /** El token nunca se serializa hacia el cliente. */
    protected $hidden = [
        'access_token_enc',
    ];

    protected function casts(): array
    {
        return [
            'access_token_enc' => 'encrypted', // cifrado en reposo
            'last_sync_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

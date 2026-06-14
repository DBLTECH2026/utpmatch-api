<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * UserResource — Serializador de salida del usuario.
 *
 * Capa: Presentación. Define EXACTAMENTE qué campos salen al cliente
 * (lista blanca de salida). Nunca incluye password ni remember_token.
 * Esto evita exposición de datos sensibles (OWASP A01/A04).
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'codigo_utp' => $this->codigo_utp,
            'carrera'    => $this->carrera,
            'ciclo'      => $this->ciclo,
            'rol'        => $this->rol,
            // Relaciones solo si fueron cargadas (evita N+1 y sobre-exposición).
            'profile'     => ProfileResource::make($this->whenLoaded('profile')),
            'courses'     => $this->whenLoaded('courses', fn () => $this->courses->map(fn ($c) => [
                'nombre' => $c->nombre,
                'estado' => $c->estado,
                'nota'   => $c->nota,
                'ciclo'  => $c->ciclo,
            ])),
            'connections' => $this->whenLoaded('connections', fn () => $this->connections->map(fn ($c) => [
                'provider'     => $c->provider,
                'status'       => $c->status,
                'last_sync_at' => $c->last_sync_at,
                // OJO: jamás se incluye access_token_enc.
            ])),
        ];
    }
}

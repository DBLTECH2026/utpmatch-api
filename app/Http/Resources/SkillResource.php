<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * SkillResource — Serializador de skill con metadatos del pivote.
 */
class SkillResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'nombre'    => $this->nombre,
            'categoria' => $this->categoria,
            // Datos del pivote profile_skills (si vienen del eager load).
            'nivel'      => $this->whenPivotLoaded('profile_skills', fn () => $this->pivot->nivel),
            'origen'     => $this->whenPivotLoaded('profile_skills', fn () => $this->pivot->origen),
            'verificado' => $this->whenPivotLoaded('profile_skills', fn () => (bool) $this->pivot->verificado),
        ];
    }
}

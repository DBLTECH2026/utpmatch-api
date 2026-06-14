<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ProfileResource — Serializador del Perfil 360.
 */
class ProfileResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'rol_objetivo'        => $this->rol_objetivo,
            'headline'            => $this->headline,
            'about'               => $this->about,
            'empleabilidad_score' => $this->empleabilidad_score,
            'autopercepcion'      => $this->autopercepcion,
            'intereses'           => $this->intereses,
            'fortalezas'          => $this->fortalezas,
            'onboarding_visto'    => (bool) $this->onboarding_visto,
            'skills'              => SkillResource::collection($this->whenLoaded('skills')),
            'updated_at'          => $this->updated_at,
        ];
    }
}

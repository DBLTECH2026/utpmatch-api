<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UpdateProfileRequest — Validación de actualización de Perfil 360.
 * Solo permite editar campos de perfil "blandos" (no toca rol ni score directo).
 */
class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // El usuario debe estar autenticado; la propiedad del perfil se
        // garantiza en el Service (siempre opera sobre $request->user()).
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'rol_objetivo'     => ['sometimes', 'nullable', 'string', 'max:120'],
            'headline'         => ['sometimes', 'nullable', 'string', 'max:160'],
            'about'            => ['sometimes', 'nullable', 'string', 'max:2000'],
            'autopercepcion'   => ['sometimes', 'nullable', 'array'],
            'autopercepcion.*' => ['string', 'max:80'],
            'intereses'        => ['sometimes', 'nullable', 'array'],
            'intereses.*'      => ['string', 'max:80'],
            'fortalezas'       => ['sometimes', 'nullable', 'array'],
            'fortalezas.*'     => ['string', 'max:80'],
            'onboarding_visto' => ['sometimes', 'boolean'],
        ];
    }
}

<?php

namespace App\Http\Requests\Auth;

use App\Support\CareerCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * RegisterRequest — Validación de registro.
 *
 * Capa: Validación (entrada). Primera línea de defensa contra datos malformados
 * e inyección (OWASP A03). Laravel rechaza con 422 antes de tocar la lógica.
 */
class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // registro público
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:120'],
            'email'      => ['required', 'email:rfc', 'max:160', 'unique:users,email'],
            // Política de contraseña robusta (A07): min 8, mayús/minús, número, símbolo
            // uncompromised() verifica contra el dataset de filtraciones (k-anonymity).
            'password'   => [
                'required', 'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
            ],
            'codigo_utp' => ['nullable', 'string', 'max:20', 'unique:users,codigo_utp'],
            // Carrera obligatoria y restringida al catálogo cerrado (define los roles del match).
            'carrera'    => ['required', 'string', Rule::in(CareerCatalog::carreras())],
            'ciclo'      => ['nullable', 'integer', 'between:1,12'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'       => 'Ese correo ya está registrado.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ];
    }

    /** Normaliza datos antes de validar (defensa en profundidad). */
    protected function prepareForValidation(): void
    {
        if ($this->has('email')) {
            $this->merge(['email' => mb_strtolower(trim((string) $this->input('email')))]);
        }
    }
}

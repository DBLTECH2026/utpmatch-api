<?php

namespace App\DataTransferObjects;

/**
 * RegisterUserData — DTO de registro.
 *
 * Patrón: DTO (Data Transfer Object) con readonly (inmutable).
 * Transporta datos YA VALIDADOS desde el Controller hacia el Service,
 * desacoplando la capa HTTP de la lógica de negocio. El Service no recibe
 * el Request crudo (evita acoplamiento y mass-assignment accidental).
 */
final readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
        public ?string $codigoUtp = null,
        public ?string $carrera = null,
        public ?int $ciclo = null,
    ) {}

    /** Crea el DTO desde el array validado del FormRequest. */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            codigoUtp: $data['codigo_utp'] ?? null,
            carrera: $data['carrera'] ?? null,
            ciclo: isset($data['ciclo']) ? (int) $data['ciclo'] : null,
        );
    }

    /** Mapea a columnas de la tabla users (sin el password en claro extra). */
    public function toUserArray(): array
    {
        return [
            'name'       => $this->name,
            'email'      => $this->email,
            'password'   => $this->password, // el modelo lo hashea (cast 'hashed')
            'codigo_utp' => $this->codigoUtp,
            'carrera'    => $this->carrera,
            'ciclo'      => $this->ciclo,
        ];
    }
}

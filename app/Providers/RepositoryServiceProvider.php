<?php

namespace App\Providers;

use App\Repositories\Contracts\ProfileRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentProfileRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * RepositoryServiceProvider — Inversión de Control (IoC).
 *
 * Enlaza cada interfaz de repositorio con su implementación concreta.
 * Gracias a esto, los Services piden la INTERFAZ por constructor y el
 * contenedor de Laravel inyecta la implementación Eloquent.
 *
 * Para cambiar de almacenamiento (p.ej. tests con un fake), basta cambiar
 * el binding aquí — cero cambios en la lógica de negocio (Open/Closed).
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string> Mapa contrato → implementación
     */
    public array $bindings = [
        UserRepositoryInterface::class    => EloquentUserRepository::class,
        ProfileRepositoryInterface::class => EloquentProfileRepository::class,
    ];

    public function register(): void
    {
        // Laravel resuelve $bindings automáticamente; método aquí por claridad.
    }
}

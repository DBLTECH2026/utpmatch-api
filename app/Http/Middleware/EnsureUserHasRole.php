<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureUserHasRole — Control de Acceso Basado en Roles (RBAC)
 * -----------------------------------------------------------
 * Restringe rutas según el rol del usuario autenticado.
 *
 * Uso en rutas:  ->middleware('role:admin')   o   'role:admin,asesor'
 *
 * Mitiga (OWASP A01 Broken Access Control):
 *  - Verifica autorización a nivel de ruta, además de la autenticación.
 *  - Falla cerrado (deny by default): sin usuario o sin rol → 403.
 *
 * Patrón: Middleware + Guard Clause.
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Deny by default: si no hay usuario autenticado, 401.
        if (! $user) {
            abort(401, 'No autenticado.');
        }

        // El usuario debe tener al menos uno de los roles requeridos.
        if (! in_array($user->rol, $roles, true)) {
            abort(403, 'No tienes permisos para esta acción.');
        }

        return $next($request);
    }
}

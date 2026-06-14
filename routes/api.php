<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — UTP+Match  (versión v1)
|--------------------------------------------------------------------------
| Versionado bajo /api/v1 para poder evolucionar sin romper clientes.
|
| Seguridad por capas de middleware:
|  - throttle:auth  → rate limit estricto en login/registro (anti fuerza bruta)
|  - throttle:api   → rate limit general
|  - auth:sanctum   → exige token Bearer válido
|  - role:...       → RBAC por rol
*/

Route::prefix('v1')->group(function () {

    // ---- Rutas públicas (con rate limit estricto) ----
    Route::middleware('throttle:auth')->prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login',    [AuthController::class, 'login']);
    });

    // ---- Rutas protegidas (token Bearer requerido) ----
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

        // Sesión / usuario actual
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me',           [AuthController::class, 'me']);

        // Perfil 360 (del usuario autenticado)
        Route::get('profile',  [ProfileController::class, 'show']);
        Route::put('profile',  [ProfileController::class, 'update']);

        // Ejemplo de ruta solo-admin (RBAC) — placeholder para crecer:
        // Route::get('admin/usuarios', [...])->middleware('role:admin');
    });
});

<?php

use App\Http\Controllers\Api\V1\AdvisorController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CareerController;
use App\Http\Controllers\Api\V1\CopilotController;
use App\Http\Controllers\Api\V1\CvController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\MatchController;
use App\Http\Controllers\Api\V1\OnboardingController;
use App\Http\Controllers\Api\V1\ProfileController;
use App\Http\Controllers\Api\V1\RouteController;
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

    // ---- Catálogo público (carreras → roles) ----
    Route::get('careers', [CareerController::class, 'index']);

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

        // Onboarding (wizard "¿qué quieres ser?")
        Route::post('onboarding/suggest', [OnboardingController::class, 'suggest']);

        // Dashboard (resumen del viaje)
        Route::get('dashboard', [DashboardController::class, 'index']);

        // Ruta & Brechas
        Route::get('route',        [RouteController::class, 'show']);
        Route::put('route/target', [RouteController::class, 'setTarget']);
        Route::get('gaps',         [RouteController::class, 'gaps']);

        // CV Inteligente
        Route::post('cv',          [CvController::class, 'generate']);
        Route::get('cv/{cv}',      [CvController::class, 'show']);
        Route::get('cv/{cv}/ats',  [CvController::class, 'ats']);

        // Empleos & Match
        Route::post('match/search',        [MatchController::class, 'search']);
        Route::get('match/{vacancy}',      [MatchController::class, 'detail']);

        // Asesores
        Route::get('advisors',          [AdvisorController::class, 'index']);
        Route::post('advisor-sessions', [AdvisorController::class, 'requestSession']);

        // Copiloto
        Route::get('copilot/nudges', [CopilotController::class, 'nudges']);
        Route::post('copilot/chat',  [CopilotController::class, 'chat']);
    });
});

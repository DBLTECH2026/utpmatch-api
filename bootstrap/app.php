<?php

use App\Http\Middleware\SecurityHeaders;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // --- Seguridad transversal de la API ---
        // Cabeceras de seguridad en TODAS las respuestas de la API.
        $middleware->api(append: [
            SecurityHeaders::class,
        ]);

        // Alias de middlewares reutilizables (RBAC).
        $middleware->alias([
            'role' => \App\Http\Middleware\EnsureUserHasRole::class,
        ]);

        // NOTA DE SEGURIDAD: NO activamos statefulApi().
        // El frontend (Next.js) consume la API con Bearer token (Sanctum personal
        // access tokens), no con cookies de sesión. Activar statefulApi provocaría
        // validación CSRF y errores 419 desde orígenes cruzados. (lección aprendida)
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Respuestas de error SIEMPRE en JSON para clientes de API.
        // Evita filtrar stack traces (A05/A09 OWASP) y unifica el contrato {errors}.

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Los datos enviados no son válidos.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'No autenticado.',
                ], 401);
            }
        });

        $exceptions->render(function (HttpExceptionInterface $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Error en la solicitud.',
                ], $e->getStatusCode());
            }
        });
    })->create();

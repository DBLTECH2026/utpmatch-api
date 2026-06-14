<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders
 * ---------------
 * Añade cabeceras de seguridad HTTP a cada respuesta de la API.
 *
 * Mitiga (OWASP Top 10):
 *  - A05 Security Misconfiguration  → cabeceras endurecidas por defecto
 *  - A03 Injection / XSS            → CSP + X-Content-Type-Options
 *  - Clickjacking                   → X-Frame-Options
 *  - Fuga de referer                → Referrer-Policy
 *
 * Patrón: Middleware (Chain of Responsibility de Laravel).
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Evita que el navegador "adivine" el MIME type (anti MIME-sniffing).
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Bloquea el embebido en iframes de terceros (anti-clickjacking).
        $response->headers->set('X-Frame-Options', 'DENY');

        // No filtrar la URL completa como referer hacia otros orígenes.
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Desactiva APIs sensibles del navegador que la API no necesita.
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // CSP mínima para una API JSON (no sirve HTML/JS propio).
        $response->headers->set('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none';");

        // Fuerza HTTPS en producción (HSTS). Solo si la petición ya es segura.
        if ($request->secure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Oculta la tecnología del servidor (reduce superficie de fingerprinting).
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}

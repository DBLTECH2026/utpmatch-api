<?php

/**
 * CORS — Cross-Origin Resource Sharing
 * ------------------------------------
 * Controla qué orígenes (frontends) pueden consumir la API.
 *
 * Seguridad (OWASP A05 Misconfiguration):
 *  - NUNCA usar '*' en allowed_origins en producción.
 *  - Lista blanca explícita por entorno vía CORS_ALLOWED_ORIGINS.
 */

return [

    // Solo aplicamos CORS a la API.
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    // Lista blanca leída del .env (coma-separada). Fallback a localhost dev.
    'allowed_origins' => array_filter(
        explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost:3000'))
    ),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'Accept', 'X-Requested-With'],

    'exposed_headers' => [],

    'max_age' => 3600,

    // Bearer token NO usa cookies → false (más seguro, evita CSRF cross-site).
    'supports_credentials' => false,

];

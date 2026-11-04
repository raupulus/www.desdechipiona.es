<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para añadir headers de seguridad HTTP a todas las respuestas.
 * Protege contra clickjacking, MIME sniffing y otros ataques comunes.
 */
class SecurityHeaders
{
    /**
     * Añade los headers de seguridad a la respuesta HTTP.
     *
     * @param  Request  $request  La petición HTTP entrante
     * @param  Closure  $next  El siguiente middleware a ejecutar
     * @return Response La respuesta HTTP con headers de seguridad
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');
        $response->headers->set('Cross-Origin-Resource-Policy', 'same-origin');

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}

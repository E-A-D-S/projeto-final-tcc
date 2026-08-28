<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Cabecalhos de seguranca aplicados a todas as respostas.
// Inclui o bloqueio de indexacao (noindex) enquanto o projeto nao estiver em uso real.
class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (!method_exists($response, 'header')) {
            return $response;
        }

        // nao indexar em nenhum buscador
        $response->header('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');

        // anti-clickjacking e sniffing
        $response->header('X-Frame-Options', 'SAMEORIGIN');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');

        // reduz superficie de APIs sensiveis do navegador
        $response->header('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // HSTS: forca HTTPS (o Render serve por HTTPS)
        if ($request->isSecure()) {
            $response->header('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // Content-Security-Policy.
        // Observacao: mantemos 'unsafe-inline'/'unsafe-eval' porque (a) o app usa
        // scripts/estilos inline e (b) o VLibras (widget de Libras) e uma aplicacao
        // Unity/WASM que precisa deles. Ainda assim o CSP trava o que mais importa:
        // origem de scripts (so 'self' + vlibras), object/embed, enquadramento (clickjacking),
        // base-uri e destino de formularios.
        // Origens do VLibras: o plugin do governo carrega o widget real do jsDelivr.
        $vlibras = 'https://vlibras.gov.br https://cdn.jsdelivr.net';
        $csp = implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "img-src 'self' data: {$vlibras}",
            "font-src 'self' data: {$vlibras}",
            "style-src 'self' 'unsafe-inline' {$vlibras}",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' {$vlibras}",
            "connect-src 'self' {$vlibras}",
            "worker-src 'self' blob:",
            "child-src 'self' blob: {$vlibras}",
            "media-src 'self' blob: {$vlibras}",
        ]);
        $response->header('Content-Security-Policy', $csp);

        return $response;
    }
}

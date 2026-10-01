<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $response = $next($request);

        /*
         * En desarrollo dejamos Vite/HMR trabajar
         * sin una CSP estricta.
         */
        if (!app()->isProduction()) {
            return $response;
        }

        $policy = implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
            "object-src 'none'",
            "script-src 'self'",
            "style-src 'self'",
            "img-src 'self' data:",
            "font-src 'self'",
            "connect-src 'self'",
            "media-src 'self'",
            "worker-src 'self'",
            "manifest-src 'self'",
        ]);

        $response->headers->set(
            'Content-Security-Policy',
            $policy
        );

        return $response;
    }
}
<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /*
         * Buscador público.
         *
         * Es relativamente permisivo porque el buscador
         * realiza peticiones mientras el usuario escribe.
         */
        RateLimiter::for(
            'buscar-asuntos',
            function (Request $request) {
                return Limit::perMinute(120)
                    ->by($request->ip());
            }
        );

        /*
         * Comprobación de títulos disponibles.
         */
        RateLimiter::for(
            'sugerencias-asuntos',
            function (Request $request) {
                return Limit::perMinute(60)
                    ->by($request->ip());
            }
        );

        /*
         * Creación de conversaciones.
         */
        RateLimiter::for(
            'crear-asunto',
            function (Request $request) {
                return Limit::perMinute(5)
                    ->by($request->ip());
            }
        );

        /*
         * Intentos para desbloquear conversaciones.
         *
         * Esto es adicional a nuestro sistema propio
         * de 3 intentos fallidos por asunto.
         */
        RateLimiter::for(
            'desbloquear-asunto',
            function (Request $request) {
                $asunto = $request->route('asunto');

                $identificador =
                    is_object($asunto)
                        ? $asunto->id
                        : (string) $asunto;

                return Limit::perMinute(10)
                    ->by(
                        $request->ip()
                        . '|'
                        . $identificador
                    );
            }
        );

        /*
         * Envío de mensajes / imágenes.
         */
        RateLimiter::for(
            'enviar-mensaje',
            function (Request $request) {
                $asunto = $request->route('asunto');

                $identificador =
                    is_object($asunto)
                        ? $asunto->id
                        : (string) $asunto;

                return Limit::perMinute(30)
                    ->by(
                        $request->ip()
                        . '|'
                        . $identificador
                    );
            }
        );

        /*
         * Login administrativo.
         */
        RateLimiter::for(
            'admin-login',
            function (Request $request) {
                return Limit::perMinute(5)
                    ->by($request->ip());
            }
        );

        /*
         * Visualización de adjuntos.
         */
        RateLimiter::for(
            'adjuntos',
            function (Request $request) {
                return Limit::perMinute(60)
                    ->by($request->ip());
            }
        );
    }
}
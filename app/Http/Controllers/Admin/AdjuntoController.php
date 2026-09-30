<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adjunto;
use App\Services\AdjuntoService;
use App\Services\CifradoConversacionService;
use Symfony\Component\HttpFoundation\Response;

class AdjuntoController extends Controller
{
    public function __construct(
        private readonly AdjuntoService $adjuntoService,
        private readonly CifradoConversacionService $cifradoService
    ) {
    }

    public function show(
        Adjunto $adjunto
    ): Response {
        $mensaje = $adjunto
            ->mensaje()
            ->withTrashed()
            ->firstOrFail();

        $asunto = $mensaje
            ->asunto()
            ->withTrashed()
            ->firstOrFail();

        if (!$asunto->clave_admin_cifrada) {
            abort(404);
        }

        $claveConversacion =
            $this->cifradoService
                ->recuperarClaveComoAdmin(
                    $asunto->clave_admin_cifrada
                );

        $contenido =
            $this->adjuntoService
                ->obtenerContenido(
                    $adjunto,
                    $claveConversacion
                );

        sodium_memzero(
            $claveConversacion
        );

        return response(
            $contenido,
            200,
            [
                'Content-Type' =>
                    $adjunto->mime,

                'Content-Disposition' =>
                    'inline',

                'X-Content-Type-Options' =>
                    'nosniff',

                'Cache-Control' =>
                    'private, no-store, no-cache',

                'Pragma' =>
                    'no-cache',

                'Expires' =>
                    '0',
            ]
        );
    }
}

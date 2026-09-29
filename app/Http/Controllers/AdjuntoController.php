<?php

namespace App\Http\Controllers;

use App\Models\Adjunto;
use App\Models\Asunto;
use App\Services\AdjuntoService;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AdjuntoController extends Controller
{
    public function __construct(
        private readonly AdjuntoService $adjuntoService
    ) {
    }

    public function show(
        Asunto $asunto,
        Adjunto $adjunto
    ): Response|RedirectResponse {
        if (
            $adjunto->mensaje->asunto_id !== $asunto->id
        ) {
            abort(404);
        }

        $claveCodificada = session()->get(
            "asuntos_desbloqueados.{$asunto->id}"
        );

        if (!$claveCodificada) {
            return redirect()
                ->route('asuntos.acceso', $asunto);
        }

        $claveConversacion = base64_decode(
            $claveCodificada,
            true
        );

        if ($claveConversacion === false) {
            abort(403);
        }

        $contenido =
            $this->adjuntoService->obtenerContenido(
                $adjunto,
                $claveConversacion
            );

        sodium_memzero($claveConversacion);

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
            ]
        );
    }
}

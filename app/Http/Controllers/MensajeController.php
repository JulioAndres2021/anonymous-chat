<?php

namespace App\Http\Controllers;

use App\Models\Asunto;
use App\Services\CifradoConversacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MensajeController extends Controller
{
    public function __construct(
        private readonly CifradoConversacionService $cifradoService
    ) {
    }

    public function store(
        Request $request,
        Asunto $asunto
    ): RedirectResponse {
        $request->validate([
            'mensaje' => [
                'required',
                'string',
                'max:10000',
            ],
        ], [
            'mensaje.required' =>
                'El mensaje es obligatorio.',

            'mensaje.max' =>
                'El mensaje no puede superar los 10.000 caracteres.',
        ]);

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
            session()->forget(
                "asuntos_desbloqueados.{$asunto->id}"
            );

            return redirect()
                ->route('asuntos.acceso', $asunto);
        }

        $contenidoCifrado =
            $this->cifradoService->cifrarMensaje(
                $request->mensaje,
                $claveConversacion
            );

        $asunto->mensajes()->create([
            'contenido_cifrado' =>
                $contenidoCifrado,
        ]);

        sodium_memzero($claveConversacion);

        return redirect()
            ->route('asuntos.chat', $asunto)
            ->with(
                'success',
                'Mensaje enviado correctamente.'
            );
    }
}

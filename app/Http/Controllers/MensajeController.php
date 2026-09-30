<?php

namespace App\Http\Controllers;

use App\Models\Asunto;
use App\Services\CifradoConversacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Services\AdjuntoService;
use App\Services\InformacionClienteService;
use Illuminate\Validation\Rules\File;

class MensajeController extends Controller
{
    public function __construct(
        private readonly CifradoConversacionService $cifradoService,
        private readonly AdjuntoService $adjuntoService,
        private readonly InformacionClienteService $clienteService
    ) {}

    public function store(Request $request, Asunto $asunto): RedirectResponse
    {
        $request->validate([
            'mensaje' => [
                'nullable',
                'string',
                'max:10000',
                'required_without:imagen',
            ],

            'imagen' => [
                'nullable',

                File::image()
                    ->types([
                        'jpg',
                        'jpeg',
                        'png',
                        'webp',
                        'gif',
                    ])
                    ->max('5mb'),
            ],
        ], [
            'mensaje.required_without' =>
            'Debe escribir un mensaje o adjuntar una imagen.',

            'mensaje.max' =>
            'El mensaje no puede superar los 10.000 caracteres.',

            'imagen.image' =>
            'El archivo debe ser una imagen válida.',

            'imagen.mimes' =>
            'La imagen debe ser JPG, JPEG, PNG, WEBP o GIF.',

            'imagen.max' =>
            'La imagen no puede superar los 5 MB.',
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

        $contenido = $request->mensaje ?? '';

        $contenidoCifrado =
            $this->cifradoService->cifrarMensaje(
                $contenido,
                $claveConversacion
            );
        $cliente = $this->clienteService->obtener($request);

        $mensaje = $asunto->mensajes()->create([
            'contenido_cifrado' =>
                $contenidoCifrado,

            'ip_cifrada' =>
                $cliente['ip_cifrada'],

            'sistema_operativo' =>
                $cliente['sistema_operativo'],

            'navegador' =>
                $cliente['navegador'],

            'user_agent' =>
                $cliente['user_agent'],

        ]);

        if ($request->hasFile('imagen')) {
            $this->adjuntoService->guardarImagen(
                $request->file('imagen'),
                $mensaje,
                $claveConversacion
            );
        }

        sodium_memzero($claveConversacion);

        return redirect()
            ->route('asuntos.chat', $asunto)
            ->with(
                'success',
                'Mensaje enviado correctamente.'
            );
    }
}
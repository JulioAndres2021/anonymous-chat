<?php

namespace App\Http\Controllers;

use App\Models\Asunto;
use App\Services\CifradoConversacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AccesoAsuntoController extends Controller
{
    public function __construct(
        private readonly CifradoConversacionService $cifradoService
    ) {
    }

    public function show(Asunto $asunto): View
    {
        return view('asuntos.acceso', compact('asunto'));
    }

    public function unlock(
        Request $request,
        Asunto $asunto
    ): RedirectResponse {
        $request->validate([
            'password' => [
                'required',
                'string',
            ],
        ], [
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        if (!Hash::check(
            $request->password,
            $asunto->password_hash
        )) {
            return back()
                ->withErrors([
                    'password' => 'Contraseña incorrecta.',
                ]);
        }

        try {
            $claveConversacion =
                $this->cifradoService->recuperarClaveConPassword(
                    $asunto->clave_password_cifrada,
                    $request->password
                );
        } catch (\Throwable $e) {
            return back()
                ->withErrors([
                    'password' => 'No fue posible desbloquear la conversación.',
                ]);
        }

        session()->put(
            "asuntos_desbloqueados.{$asunto->id}",
            base64_encode($claveConversacion)
        );

        sodium_memzero($claveConversacion);

        return redirect()
            ->route('asuntos.chat', $asunto);
    }

    public function chat(Asunto $asunto): View|RedirectResponse
    {
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

        $mensajes = $asunto
            ->mensajes()
            ->oldest()
            ->get()
            ->map(function ($mensaje) use ($claveConversacion) {

                $mensaje->contenido_descifrado =
                    $this->cifradoService->descifrarMensaje(
                        $mensaje->contenido_cifrado,
                        $claveConversacion
                    );

                return $mensaje;
            });

        sodium_memzero($claveConversacion);

        return view(
            'asuntos.chat',
            compact('asunto', 'mensajes')
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Asunto;
use App\Services\CifradoConversacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Services\IntentoAccesoService;

class AccesoAsuntoController extends Controller
{
    public function __construct(
    private readonly CifradoConversacionService $cifradoService,
    private readonly IntentoAccesoService $intentoAccesoService
) {
}

    public function show(Asunto $asunto): View
    {
        return view('asuntos.acceso', compact('asunto'));
    }

   public function unlock(Request $request, Asunto $asunto): RedirectResponse
    {
        $request->validate([
            'password' => [
                'required',
                'string',
            ],
        ], [
            'password.required' =>
                'La contraseña es obligatoria.',
        ]);

        $registro =
            $this->intentoAccesoService->obtenerRegistro(
                $asunto,
                $request
            );

        if (
            $this->intentoAccesoService
                ->estaBloqueado($registro)
        ) {
            return back()
                ->withErrors([
                    'password' =>
                        'Demasiados intentos fallidos. Intente nuevamente más tarde.',
                ]);
        }

        if (
            !Hash::check(
                $request->password,
                $asunto->password_hash
            )
        ) {
            $registro =
                $this->intentoAccesoService
                    ->registrarFallo($registro);

            if (
                $this->intentoAccesoService
                    ->estaBloqueado($registro)
            ) {
                return back()
                    ->withErrors([
                        'password' =>
                            'Se alcanzó el máximo de intentos. Acceso bloqueado temporalmente.',
                    ]);
            }

            $restantes =
                $this->intentoAccesoService
                    ->intentosRestantes($registro);

            return back()
                ->withErrors([
                    'password' =>
                        "Contraseña incorrecta. Intentos restantes: {$restantes}.",
                ]);
        }

        try {
            $claveConversacion =
                $this->cifradoService
                    ->recuperarClaveConPassword(
                        $asunto->clave_password_cifrada,
                        $request->password
                    );
        } catch (\Throwable $e) {
            return back()
                ->withErrors([
                    'password' =>
                        'No fue posible desbloquear la conversación.',
                ]);
        }

        $this->intentoAccesoService
            ->limpiarIntentos($registro);

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
            ->with('adjuntos')
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

    public function lock(Request $request, Asunto $asunto): RedirectResponse 
    {
        $request->session()->forget(
            "asuntos_desbloqueados.{$asunto->id}"
        );

        return redirect()
            ->route('asuntos.acceso', $asunto)
            ->with(
                'success',
                'Conversación bloqueada correctamente.'
            );
    }
}
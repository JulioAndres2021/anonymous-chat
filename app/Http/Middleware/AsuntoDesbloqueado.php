<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AsuntoDesbloqueado
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $asunto = $request->route('asunto');

        if (!$asunto) {
            abort(404);
        }

        $clave = session()->get(
            "asuntos_desbloqueados.{$asunto->id}"
        );

        if (!$clave) {
            return redirect()
                ->route('asuntos.acceso', $asunto);
        }

        return $next($request);
    }
}

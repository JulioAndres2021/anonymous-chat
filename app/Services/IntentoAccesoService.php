<?php

namespace App\Services;

use App\Models\Asunto;
use App\Models\IntentoAcceso;
use Illuminate\Http\Request;

class IntentoAccesoService
{
    private const MAX_INTENTOS = 3;

    private const MINUTOS_BLOQUEO = 15;

    public function __construct(
        private readonly InformacionClienteService $clienteService
    ) {
    }

    public function obtenerRegistro(
        Asunto $asunto,
        Request $request
    ): IntentoAcceso {
        $cliente = $this->clienteService->obtener($request);

        return IntentoAcceso::firstOrCreate(
            [
                'asunto_id' => $asunto->id,
                'ip_hash' => $cliente['ip_hash'],
            ],
            [
                'ip_cifrada' => $cliente['ip_cifrada'],
                'sistema_operativo' =>
                    $cliente['sistema_operativo'],

                'navegador' =>
                    $cliente['navegador'],

                'user_agent' =>
                    $cliente['user_agent'],

                'intentos' => 0,

            ]
        );
    }

    public function estaBloqueado(
        IntentoAcceso $registro
    ): bool {
        return $registro->bloqueado_hasta !== null
            && $registro->bloqueado_hasta->isFuture();
    }

    public function registrarFallo(
        IntentoAcceso $registro
    ): IntentoAcceso {
        $registro->increment('intentos');

        $registro->refresh();

        $registro->ultimo_intento_at = now();

        if ($registro->intentos >= self::MAX_INTENTOS) {
            $registro->bloqueado_hasta =
                now()->addMinutes(
                    self::MINUTOS_BLOQUEO
                );
        }

        $registro->save();

        return $registro;
    }

    public function limpiarIntentos(
        IntentoAcceso $registro
    ): void {
        $registro->update([
            'intentos' => 0,
            'bloqueado_hasta' => null,
        ]);
    }

    public function intentosRestantes(
        IntentoAcceso $registro
    ): int {
        return max(
            0,
            self::MAX_INTENTOS - $registro->intentos
        );
    }
}

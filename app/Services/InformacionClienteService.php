<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use RuntimeException;

class InformacionClienteService
{
    public function obtener(Request $request): array
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent();

        $claveHash = config('app.ip_hash_key');

        if (!$claveHash) {
            throw new RuntimeException(
                'IP_HASH_KEY no está configurada.'
            );
        }

        return [
            'ip_cifrada' => $ip
                ? Crypt::encryptString($ip)
                : null,

            'ip_hash' => $ip
                ? hash_hmac(
                    'sha256',
                    $ip,
                    $claveHash
                )
                : hash_hmac(
                    'sha256',
                    'unknown',
                    $claveHash
                ),

            'sistema_operativo' =>
                $this->detectarSistemaOperativo($userAgent),

            'navegador' =>
                $this->detectarNavegador($userAgent),

            'user_agent' => $userAgent,
        ];
    }

    private function detectarSistemaOperativo(
        ?string $userAgent
    ): ?string {
        if (!$userAgent) {
            return null;
        }

        $ua = strtolower($userAgent);

        return match (true) {
            str_contains($ua, 'windows nt 10.0')
                => 'Windows',

            str_contains($ua, 'windows')
                => 'Windows',

            str_contains($ua, 'android')
                => 'Android',

            str_contains($ua, 'iphone'),
            str_contains($ua, 'ipad')
                => 'iOS',

            str_contains($ua, 'mac os x'),
            str_contains($ua, 'macintosh')
                => 'macOS',

            str_contains($ua, 'linux')
                => 'Linux',

            default => 'Desconocido',
        };
    }

    private function detectarNavegador(
        ?string $userAgent
    ): ?string {
        if (!$userAgent) {
            return null;
        }

        $ua = strtolower($userAgent);

        return match (true) {
            str_contains($ua, 'edg/')
                => 'Edge',

            str_contains($ua, 'opr/')
                => 'Opera',

            str_contains($ua, 'firefox/')
                => 'Firefox',

            str_contains($ua, 'chrome/')
                => 'Chrome',

            str_contains($ua, 'safari/')
                => 'Safari',

            default => 'Desconocido',
        };
    }
}

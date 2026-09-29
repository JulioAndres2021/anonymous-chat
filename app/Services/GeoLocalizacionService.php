<?php

namespace App\Services;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Throwable;

class GeoLocalizacionService
{
    public function obtener(?string $ip): array
    {
        $resultado = [
            'pais' => null,
            'provincia' => null,
            'ciudad' => null,
        ];

        if (!$ip) {
            return $resultado;
        }

        if (!$this->esIpPublica($ip)) {
            return $resultado;
        }

        $ruta = config('app.geoip_database_path');

        if (!$ruta || !is_file($ruta)) {
            return $resultado;
        }

        try {
            $reader = new Reader($ruta);

            $registro = $reader->city($ip);

            $resultado = [
                'pais' =>
                    $registro->country->name
                    ?: null,

                'provincia' =>
                    $registro->mostSpecificSubdivision->name
                    ?: null,

                'ciudad' =>
                    $registro->city->name
                    ?: null,
            ];

            $reader->close();

            return $resultado;

        } catch (AddressNotFoundException) {
            return $resultado;

        } catch (Throwable) {
            return $resultado;
        }
    }

    private function esIpPublica(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE
            | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }
}
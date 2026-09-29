<?php

namespace Tests\Unit;

use App\Services\GeoLocalizacionService;
use PHPUnit\Framework\TestCase;

class GeoLocalizacionServiceTest extends TestCase
{
    public function test_ip_local_no_genera_ubicacion(): void
    {
        $service =
            new GeoLocalizacionService();

        $resultado =
            $service->obtener('127.0.0.1');

        $this->assertNull(
            $resultado['pais']
        );

        $this->assertNull(
            $resultado['provincia']
        );

        $this->assertNull(
            $resultado['ciudad']
        );
    }
}
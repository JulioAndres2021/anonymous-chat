<?php

namespace Tests\Feature;

use App\Models\PurgaArchivo;
use App\Services\PurgaArchivoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PurgaArchivoTest extends TestCase
{
    use RefreshDatabase;

    public function test_purga_archivo_existente(): void
    {
        Storage::fake('local');

        $ruta =
            'anonymous-chat/test.enc';

        Storage::disk('local')
            ->put(
                $ruta,
                'cifrado'
            );

        $purga =
            PurgaArchivo::create([
                'disk' => 'local',
                'ruta' => $ruta,
            ]);

        $resultado =
            app(
                PurgaArchivoService::class
            )->procesar($purga);

        $this->assertTrue(
            $resultado
        );

        Storage::disk('local')
            ->assertMissing($ruta);

        $purga->refresh();

        $this->assertNotNull(
            $purga->purgado_at
        );

        $this->assertSame(
            1,
            $purga->intentos
        );
    }

    public function test_archivo_ya_inexistente_se_considera_purgado(): void
    {
        Storage::fake('local');

        $purga =
            PurgaArchivo::create([
                'disk' => 'local',

                'ruta' =>
                    'anonymous-chat/no-existe.enc',
            ]);

        $resultado =
            app(
                PurgaArchivoService::class
            )->procesar($purga);

        $this->assertTrue(
            $resultado
        );

        $purga->refresh();

        $this->assertNotNull(
            $purga->purgado_at
        );
    }
}
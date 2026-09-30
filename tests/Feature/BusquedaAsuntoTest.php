<?php

namespace Tests\Feature;

use App\Models\Asunto;
use App\Services\CifradoConversacionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BusquedaAsuntoTest extends TestCase
{
    use RefreshDatabase;

    private function crearAsunto(
        string $titulo,
        string $slug
    ): Asunto {
        $service =
            app(
                CifradoConversacionService::class
            );

        $clave =
            $service
                ->generarClaveConversacion();

        $password =
            'password123';

        $asunto = Asunto::create([
            'titulo' => $titulo,
            'slug' => $slug,

            'password_hash' =>
                Hash::make($password),

            'clave_password_cifrada' =>
                $service
                    ->protegerClaveConPassword(
                        $clave,
                        $password
                    ),

            'clave_admin_cifrada' =>
                null,

            'expira_at' =>
                now()->addDay(),
        ]);

        sodium_memzero($clave);

        return $asunto;
    }

    public function test_busqueda_encuentra_asunto(): void
    {
        $this->crearAsunto(
            'Mascota Perdida',
            'mascota-perdida'
        );

        $this->crearAsunto(
            'Pregunta Laravel',
            'pregunta-laravel'
        );

        $response = $this->getJson(
            route(
                'asuntos.buscar',
                ['q' => 'Mascota']
            )
        );

        $response->assertOk();

        $response->assertJsonFragment([
            'titulo' =>
                'Mascota Perdida',
        ]);

        $response->assertJsonMissing([
            'titulo' =>
                'Pregunta Laravel',
        ]);
    }

    public function test_titulo_duplicado_devuelve_sugerencias(): void
    {
        $this->crearAsunto(
            'Mascota Perdida',
            'mascota-perdida'
        );

        $response = $this->getJson(
            route(
                'asuntos.sugerencias',
                [
                    'titulo' =>
                        'Mascota Perdida',
                ]
            )
        );

        $response->assertOk();

        $response->assertJson([
            'existe' => true,
        ]);

        $this->assertNotEmpty(
            $response->json(
                'sugerencias'
            )
        );
    }
}
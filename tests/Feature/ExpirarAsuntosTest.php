<?php

namespace Tests\Feature;

use App\Models\Asunto;
use App\Services\CifradoConversacionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExpirarAsuntosTest extends TestCase
{
    use RefreshDatabase;

    public function test_asunto_vencido_es_eliminado_logicamente(): void
    {
        $service =
            app(CifradoConversacionService::class);

        $password = 'password123';

        $clave =
            $service->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' => 'Asunto Vencido',
            'slug' => 'asunto-vencido',

            'password_hash' =>
                Hash::make($password),

            'clave_password_cifrada' =>
                $service->protegerClaveConPassword(
                    $clave,
                    $password
                ),

            'clave_admin_cifrada' => null,

            'expira_at' =>
                now()->subMinute(),
        ]);

        $contenidoCifrado =
            $service->cifrarMensaje(
                'Mensaje vencido',
                $clave
            );

        $mensaje =
            $asunto->mensajes()->create([
                'contenido_cifrado' =>
                    $contenidoCifrado,
            ]);

        sodium_memzero($clave);

        $this->artisan(
            'anonymous:expirar-asuntos'
        )
            ->expectsOutput(
                'Asuntos expirados: 1'
            )
            ->assertSuccessful();

        $this->assertSoftDeleted(
            'asuntos',
            [
                'id' => $asunto->id,
            ]
        );

        $this->assertSoftDeleted(
            'mensajes',
            [
                'id' => $mensaje->id,
            ]
        );
    }
}

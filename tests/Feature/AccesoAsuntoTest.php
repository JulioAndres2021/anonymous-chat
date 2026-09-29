<?php

namespace Tests\Feature;

use App\Models\Asunto;
use App\Services\CifradoConversacionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccesoAsuntoTest extends TestCase
{
    use RefreshDatabase;

    private function crearAsunto(): Asunto
    {
        $cifrado =
            app(CifradoConversacionService::class);

        $password = 'password123';

        $clave =
            $cifrado->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' => 'Asunto Test',
            'slug' => 'asunto-test',

            'password_hash' =>
                Hash::make($password),

            'clave_password_cifrada' =>
                $cifrado->protegerClaveConPassword(
                    $clave,
                    $password
                ),

            'clave_admin_cifrada' => null,

            'expira_at' =>
                now()->addDay(),
        ]);

        sodium_memzero($clave);

        return $asunto;
    }

    public function test_password_incorrecta_no_permite_acceso(): void
    {
        $asunto = $this->crearAsunto();

        $response = $this->post(
            route('asuntos.unlock', $asunto),
            [
                'password' => 'incorrecta',
            ]
        );

        $response->assertSessionHasErrors('password');

        $this->assertFalse(
            session()->has(
                "asuntos_desbloqueados.{$asunto->id}"
            )
        );
    }

    public function test_password_correcta_desbloquea_asunto(): void
    {
        $asunto = $this->crearAsunto();

        $response = $this->post(
            route('asuntos.unlock', $asunto),
            [
                'password' => 'password123',
            ]
        );

        $response->assertRedirect(
            route('asuntos.chat', $asunto)
        );

        $this->assertTrue(
            session()->has(
                "asuntos_desbloqueados.{$asunto->id}"
            )
        );
    }

    public function test_tres_intentos_fallidos_bloquean_acceso(): void
    {
        $asunto = $this->crearAsunto();

        for ($i = 0; $i < 3; $i++) {
            $this->post(
                route('asuntos.unlock', $asunto),
                [
                    'password' => 'incorrecta',
                ]
            );
        }

        $this->assertDatabaseHas(
            'intentos_acceso',
            [
                'asunto_id' => $asunto->id,
                'intentos' => 3,
            ]
        );
    }
}

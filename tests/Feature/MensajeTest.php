<?php

namespace Tests\Feature;

use App\Models\Asunto;
use App\Services\CifradoConversacionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MensajeTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_desbloqueado_puede_responder(): void
    {
        $service =
            app(CifradoConversacionService::class);

        $password = 'password123';

        $clave =
            $service->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' => 'Asunto Mensaje',
            'slug' => 'asunto-mensaje',

            'password_hash' =>
                Hash::make($password),

            'clave_password_cifrada' =>
                $service->protegerClaveConPassword(
                    $clave,
                    $password
                ),

            'clave_admin_cifrada' => null,

            'expira_at' =>
                now()->addDay(),
        ]);

        $this->withSession([
            "asuntos_desbloqueados.{$asunto->id}"
                => base64_encode($clave),
        ]);

        $response = $this->post(
            route('mensajes.store', $asunto),
            [
                'mensaje' =>
                    'Mensaje de prueba cifrado.',
            ]
        );

        $response->assertRedirect(
            route('asuntos.chat', $asunto)
        );

        $mensaje = $asunto
            ->mensajes()
            ->first();

        $this->assertNotNull($mensaje);

        $this->assertStringNotContainsString(
            'Mensaje de prueba cifrado.',
            $mensaje->contenido_cifrado
        );

        $contenidoDescifrado =
            $service->descifrarMensaje(
                $mensaje->contenido_cifrado,
                $clave
            );

        $this->assertSame(
            'Mensaje de prueba cifrado.',
            $contenidoDescifrado
        );

        sodium_memzero($clave);
    }

    public function test_usuario_sin_desbloquear_no_puede_responder(): void
    {
        $service =
            app(CifradoConversacionService::class);

        $password = 'password123';

        $clave =
            $service->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' => 'Asunto Bloqueado',
            'slug' => 'asunto-bloqueado',

            'password_hash' =>
                Hash::make($password),

            'clave_password_cifrada' =>
                $service->protegerClaveConPassword(
                    $clave,
                    $password
                ),

            'clave_admin_cifrada' => null,

            'expira_at' =>
                now()->addDay(),
        ]);

        $response = $this->post(
            route('mensajes.store', $asunto),
            [
                'mensaje' =>
                    'No debería guardarse.',
            ]
        );

        $response->assertRedirect(
            route('asuntos.acceso', $asunto)
        );

        $this->assertDatabaseCount(
            'mensajes',
            0
        );

        sodium_memzero($clave);
    }
}

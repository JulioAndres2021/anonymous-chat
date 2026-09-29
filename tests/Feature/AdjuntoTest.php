<?php

namespace Tests\Feature;

use App\Models\Asunto;
use App\Services\CifradoConversacionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdjuntoTest extends TestCase
{
    use RefreshDatabase;

    public function test_imagen_se_guarda_cifrada(): void
    {
        Storage::fake('local');

        $service =
            app(CifradoConversacionService::class);

        $password = 'password123';

        $clave =
            $service->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' => 'Asunto Imagen',
            'slug' => 'asunto-imagen',

            'password_hash' =>
                Hash::make($password),

            'clave_password_cifrada' =>
                $service->protegerClaveConPassword(
                    $clave,
                    $password
                ),

            'clave_admin_cifrada' => null,

            'expira_at' => now()->addDay(),
        ]);

        $this->withSession([
            "asuntos_desbloqueados.{$asunto->id}"
                => base64_encode($clave),
        ]);

        $imagen = UploadedFile::fake()
            ->image('prueba.jpg');

        $response = $this->post(
            route('mensajes.store', $asunto),
            [
                'mensaje' => 'Imagen de prueba.',
                'imagen' => $imagen,
            ]
        );

        $response->assertRedirect(
            route('asuntos.chat', $asunto)
        );

        $mensaje =
            $asunto->mensajes()->first();

        $adjunto =
            $mensaje->adjuntos()->first();

        $this->assertNotNull($adjunto);

        Storage::disk('local')
            ->assertExists($adjunto->ruta);

        $contenidoGuardado =
            Storage::disk('local')
                ->get($adjunto->ruta);

        $this->assertStringNotContainsString(
            'JFIF',
            $contenidoGuardado
        );

        sodium_memzero($clave);
    }
}

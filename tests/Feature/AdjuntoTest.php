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

    public function test_no_permite_archivo_que_no_es_imagen(): void
    {
        $service =
            app(
                CifradoConversacionService::class
            );

        $password = 'password123';

        $clave =
            $service->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' =>
                'Archivo Invalido',

            'slug' =>
                'archivo-invalido',

            'password_hash' =>
                Hash::make($password),

            'clave_password_cifrada' =>
                $service
                    ->protegerClaveConPassword(
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

        $archivo =
            UploadedFile::fake()
                ->create(
                    'malicioso.txt',
                    10,
                    'text/plain'
                );

        $response = $this->post(
            route(
                'mensajes.store',
                $asunto
            ),
            [
                'mensaje' =>
                    'Archivo inválido',

                'imagen' =>
                    $archivo,
            ]
        );

        $response->assertSessionHasErrors(
            'imagen'
        );

        $this->assertDatabaseCount(
            'adjuntos',
            0
        );

        sodium_memzero($clave);
    }

    public function test_no_permite_imagen_superior_a_cinco_mb(): void
    {
        $service =
            app(
                CifradoConversacionService::class
            );

        $password = 'password123';

        $clave =
            $service->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' =>
                'Imagen Grande',

            'slug' =>
                'imagen-grande',

            'password_hash' =>
                Hash::make($password),

            'clave_password_cifrada' =>
                $service
                    ->protegerClaveConPassword(
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

        $archivo =
            UploadedFile::fake()
                ->create(
                    'grande.jpg',
                    6000,
                    'image/jpeg'
                );

        $response = $this->post(
            route(
                'mensajes.store',
                $asunto
            ),
            [
                'mensaje' =>
                    'Imagen demasiado grande',

                'imagen' =>
                    $archivo,
            ]
        );

        $response->assertSessionHasErrors(
            'imagen'
        );

        $this->assertDatabaseCount(
            'adjuntos',
            0
        );

        sodium_memzero($clave);
    }

    public function test_usuario_puede_crear_asunto_con_imagen_inicial(): void
    {
        Storage::fake('local');

        $archivo =
            UploadedFile::fake()
                ->image(
                    'foto.jpg',
                    800,
                    600
                );

        $response = $this->post(
            route('asuntos.store'),
            [
                'titulo' =>
                    'Asunto con imagen inicial',

                'password' =>
                    'password123',

                'password_confirmation' =>
                    'password123',

                'expira_at' =>
                    now()
                        ->addDay()
                        ->format('Y-m-d\TH:i'),

                'mensaje' =>
                    'Mensaje inicial con imagen',

                'imagen' =>
                    $archivo,
            ]
        );

        $response->assertRedirect(
            route('asuntos.index')
        );

        $asunto =
            Asunto::where(
                'titulo',
                'Asunto con imagen inicial'
            )
            ->firstOrFail();

        $mensaje =
            $asunto
                ->mensajes()
                ->firstOrFail();

        $adjunto =
            $mensaje
                ->adjuntos()
                ->firstOrFail();

        $this->assertSame(
            'image/jpeg',
            $adjunto->mime
        );

        $this->assertNotNull(
            $adjunto->ruta
        );

        Storage::disk('local')
            ->assertExists(
                $adjunto->ruta
            );
    }
}

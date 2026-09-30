<?php

namespace Tests\Feature;

use App\Models\Asunto;
use App\Models\User;
use App\Services\CifradoConversacionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class AdminAsuntoTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_restaurar_asunto_eliminado(): void
    {
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' =>
                Hash::make('Admin1234!'),
        ]);

        $service =
            app(CifradoConversacionService::class);

        $clave =
            $service->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' => 'Asunto Eliminado',
            'slug' => 'asunto-eliminado',

            'password_hash' =>
                Hash::make('password123'),

            'clave_password_cifrada' =>
                $service->protegerClaveConPassword(
                    $clave,
                    'password123'
                ),

            'clave_admin_cifrada' => null,

            'expira_at' =>
                now()->subDay(),
        ]);

        sodium_memzero($clave);

        $asunto->delete();

        $this->actingAs($admin);

        $response = $this->post(
            route(
                'admin.papelera.restaurar',
                $asunto->id
            )
        );

        $response->assertRedirect(
            route('admin.papelera')
        );

        $asunto->refresh();

        $this->assertNull(
            $asunto->deleted_at
        );

        $this->assertTrue(
            $asunto->expira_at->isFuture()
        );
    }

    public function test_admin_puede_ver_mensaje_descifrado(): void
    {
        config()->set(
            'app.anonymous_admin_key',
            base64_encode(
                random_bytes(
                    SODIUM_CRYPTO_SECRETBOX_KEYBYTES
                )
            )
        );

        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' =>
                Hash::make('Admin1234!'),
        ]);

        $service =
            app(CifradoConversacionService::class);

        $password = 'password123';

        $clave =
            $service->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' => 'Asunto Admin',
            'slug' => 'asunto-admin',

            'password_hash' =>
                Hash::make($password),

            'clave_password_cifrada' =>
                $service->protegerClaveConPassword(
                    $clave,
                    $password
                ),

            'clave_admin_cifrada' =>
                $service->protegerClaveParaAdmin(
                    $clave
                ),

            'expira_at' =>
                now()->addDay(),
        ]);

        $asunto->mensajes()->create([
            'contenido_cifrado' =>
                $service->cifrarMensaje(
                    'Mensaje visible para admin',
                    $clave
                ),
        ]);

        sodium_memzero($clave);

        $this->actingAs($admin);

        $response = $this->get(
            route(
                'admin.asuntos.show',
                $asunto
            )
        );

        $response->assertOk();

        $response->assertSee(
            'Mensaje visible para admin'
        );
    }

    public function test_admin_puede_eliminar_asunto_definitivamente(): void
    {
        Storage::fake('local');

        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' =>
                Hash::make('Admin1234!'),
        ]);

        $service =
            app(CifradoConversacionService::class);

        $clave =
            $service->generarClaveConversacion();

        $asunto = Asunto::create([
            'titulo' => 'Eliminar Definitivo',
            'slug' => 'eliminar-definitivo',

            'password_hash' =>
                Hash::make('password123'),

            'clave_password_cifrada' =>
                $service->protegerClaveConPassword(
                    $clave,
                    'password123'
                ),

            'clave_admin_cifrada' => null,

            'expira_at' =>
                now()->subDay(),
        ]);

        $mensaje =
            $asunto->mensajes()->create([
                'contenido_cifrado' =>
                    $service->cifrarMensaje(
                        'Mensaje a eliminar',
                        $clave
                    ),
            ]);

        $ruta =
            'anonymous-chat/prueba.enc';

        Storage::disk('local')->put(
            $ruta,
            'contenido-cifrado'
        );

        $adjunto =
            $mensaje->adjuntos()->create([
                'nombre_original_cifrado' =>
                    encrypt('prueba.jpg'),

                'ruta' => $ruta,
                'mime' => 'image/jpeg',
                'tamano' => 100,
            ]);

        $adjunto->delete();
        $mensaje->delete();
        $asunto->delete();

        sodium_memzero($clave);

        $this->actingAs($admin);

        $response = $this->delete(
            route(
                'admin.papelera.destroy',
                $asunto->id
            )
        );

        $response->assertRedirect(
            route('admin.papelera')
        );

        $this->assertDatabaseMissing(
            'asuntos',
            [
                'id' => $asunto->id,
            ]
        );

        $this->assertDatabaseMissing(
            'mensajes',
            [
                'id' => $mensaje->id,
            ]
        );

        $this->assertDatabaseMissing(
            'adjuntos',
            [
                'id' => $adjunto->id,
            ]
        );

        Storage::disk('local')
            ->assertMissing($ruta);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Asunto;
use App\Models\User;
use App\Services\CifradoConversacionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

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
}

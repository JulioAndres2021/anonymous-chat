<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_no_autenticado_no_accede_al_dashboard(): void
    {
        $response = $this->get(
            route('admin.dashboard')
        );

        $response->assertRedirect(
            route('admin.login')
        );
    }

    public function test_admin_puede_iniciar_sesion(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' =>
                Hash::make('Admin1234!'),
        ]);

        $response = $this->post(
            route('admin.login.store'),
            [
                'email' => 'admin@test.com',
                'password' => 'Admin1234!',
            ]
        );

        $response->assertRedirect(
            route('admin.dashboard')
        );

        $this->assertAuthenticatedAs($user);
    }

    public function test_password_incorrecta_no_autentica(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' =>
                Hash::make('Admin1234!'),
        ]);

        $this->post(
            route('admin.login.store'),
            [
                'email' => 'admin@test.com',
                'password' => 'incorrecta',
            ]
        );

        $this->assertGuest();
    }

    public function test_admin_puede_cerrar_sesion(): void
    {
        $user = User::create([
            'name' => 'Administrador',
            'email' => 'admin@test.com',
            'password' =>
                Hash::make('Admin1234!'),
        ]);

        $this->actingAs($user);

        $response = $this->post(
            route('admin.logout')
        );

        $response->assertRedirect(
            route('admin.login')
        );

        $this->assertGuest();
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleTest extends TestCase
{
    public function test_usuario_puede_cambiar_a_ingles(): void
    {
        $response = $this->get(
            route('locale.change', 'en')
        );

        $response->assertRedirect();

        $this->assertSame(
            'en',
            session('locale')
        );
    }

    public function test_usuario_puede_cambiar_a_espanol(): void
    {
        $response = $this->get(
            route('locale.change', 'es')
        );

        $response->assertRedirect();

        $this->assertSame(
            'es',
            session('locale')
        );
    }

    public function test_idioma_invalido_devuelve_404(): void
    {
        $response = $this->get(
            route('locale.change', 'fr')
        );

        $response->assertNotFound();
    }
}

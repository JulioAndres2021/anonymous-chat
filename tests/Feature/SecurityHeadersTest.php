<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_respuesta_web_incluye_headers_de_seguridad(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $response->assertHeader(
            'X-Content-Type-Options',
            'nosniff'
        );

        $response->assertHeader(
            'X-Frame-Options',
            'DENY'
        );

        $response->assertHeader(
            'Referrer-Policy',
            'no-referrer'
        );

        $response->assertHeader(
            'Permissions-Policy',
            'camera=(), microphone=(), geolocation=()'
        );
    }

    public function test_hsts_se_aplica_en_produccion_https(): void
    {
        app()->detectEnvironment(
            fn () => 'production'
        );

        $response = $this->get(
            'https://localhost/'
        );

        $response->assertOk();

        $response->assertHeader(
            'Strict-Transport-Security',
            'max-age=31536000'
        );
    }
}
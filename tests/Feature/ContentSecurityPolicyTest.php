<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentSecurityPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_csp_se_aplica_en_produccion(): void
    {
        app()->detectEnvironment(
            fn () => 'production'
        );

        $response = $this->get('/');

        $response->assertOk();

        $response->assertHeader(
            'Content-Security-Policy'
        );

        $policy =
            $response->headers->get(
                'Content-Security-Policy'
            );

        $this->assertStringContainsString(
            "default-src 'self'",
            $policy
        );

        $this->assertStringContainsString(
            "script-src 'self'",
            $policy
        );

        $this->assertStringContainsString(
            "style-src 'self'",
            $policy
        );

        $this->assertStringContainsString(
            "frame-ancestors 'none'",
            $policy
        );

        $this->assertStringNotContainsString(
            "'unsafe-inline'",
            $policy
        );
    }

    public function test_csp_no_se_aplica_en_desarrollo(): void
    {
        app()->detectEnvironment(
            fn () => 'local'
        );

        $response = $this->get('/');

        $response->assertOk();

        $response->assertHeaderMissing(
            'Content-Security-Policy'
        );
    }
    
}
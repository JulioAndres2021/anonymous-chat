<?php

namespace Tests\Feature;

use Tests\TestCase;

class RateLimitTest extends TestCase
{
    public function test_login_admin_tiene_rate_limit(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(
                route('admin.login.store'),
                [
                    'email' =>
                        'noexiste@example.com',

                    'password' =>
                        'password-incorrecta',
                ]
            );
        }

        $response = $this->post(
            route('admin.login.store'),
            [
                'email' =>
                    'noexiste@example.com',

                'password' =>
                    'password-incorrecta',
            ]
        );

        $response->assertRedirect(
            route('admin.login')
        );

        $response->assertSessionHasErrors(
            'email'
        );
    }
}
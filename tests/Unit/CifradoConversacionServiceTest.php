<?php

namespace Tests\Unit;

use App\Services\CifradoConversacionService;
use RuntimeException;
use Tests\TestCase;

class CifradoConversacionServiceTest extends TestCase
{
    public function test_puede_cifrar_y_descifrar_un_mensaje(): void
    {
        $service = new CifradoConversacionService();

        $password = 'password-seguro-123';
        $mensajeOriginal = 'Este mensaje es secreto.';

        $claveConversacion =
            $service->generarClaveConversacion();

        $claveProtegida =
            $service->protegerClaveConPassword(
                $claveConversacion,
                $password
            );

        $mensajeCifrado =
            $service->cifrarMensaje(
                $mensajeOriginal,
                $claveConversacion
            );

        $claveRecuperada =
            $service->recuperarClaveConPassword(
                $claveProtegida,
                $password
            );

        $mensajeDescifrado =
            $service->descifrarMensaje(
                $mensajeCifrado,
                $claveRecuperada
            );

        $this->assertSame(
            $mensajeOriginal,
            $mensajeDescifrado
        );

        $this->assertStringNotContainsString(
            $mensajeOriginal,
            $mensajeCifrado
        );
    }

    public function test_password_incorrecta_no_recupera_la_clave(): void
    {
        $service = new CifradoConversacionService();

        $claveConversacion =
            $service->generarClaveConversacion();

        $claveProtegida =
            $service->protegerClaveConPassword(
                $claveConversacion,
                'password-correcta'
            );

        $this->expectException(RuntimeException::class);

        $service->recuperarClaveConPassword(
            $claveProtegida,
            'password-incorrecta'
        );
    }

    public function test_admin_puede_recuperar_clave_de_conversacion(): void
    {
        config()->set(
            'app.anonymous_admin_key',
            base64_encode(
                random_bytes(
                    SODIUM_CRYPTO_SECRETBOX_KEYBYTES
                )
            )
        );

        $service =
            new CifradoConversacionService();

        $claveOriginal =
            $service->generarClaveConversacion();

        $claveProtegida =
            $service->protegerClaveParaAdmin(
                $claveOriginal
            );

        $claveRecuperada =
            $service->recuperarClaveComoAdmin(
                $claveProtegida
            );

        $this->assertSame(
            $claveOriginal,
            $claveRecuperada
        );

        sodium_memzero(
            $claveOriginal
        );

        sodium_memzero(
            $claveRecuperada
        );
    }
}
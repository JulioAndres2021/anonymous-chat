<?php

namespace Tests\Unit;

use App\Services\CifradoConversacionService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

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
}

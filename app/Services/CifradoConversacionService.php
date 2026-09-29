<?php

namespace App\Services;

use RuntimeException;

class CifradoConversacionService
{
    private const VERSION = 1;

    public function generarClaveConversacion(): string
    {
        return random_bytes(SODIUM_CRYPTO_SECRETBOX_KEYBYTES);
    }

    public function protegerClaveConPassword(
        string $claveConversacion,
        string $password
    ): string {
        $salt = random_bytes(SODIUM_CRYPTO_PWHASH_SALTBYTES);

        $claveDerivada = sodium_crypto_pwhash(
            SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
            $password,
            $salt,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
        );

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        $cifrado = sodium_crypto_secretbox(
            $claveConversacion,
            $nonce,
            $claveDerivada
        );

        sodium_memzero($claveDerivada);

        return json_encode([
            'version' => self::VERSION,
            'salt' => base64_encode($salt),
            'nonce' => base64_encode($nonce),
            'ciphertext' => base64_encode($cifrado),
        ], JSON_THROW_ON_ERROR);
    }

    public function recuperarClaveConPassword(
        string $payload,
        string $password
    ): string {
        $datos = json_decode(
            $payload,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (
            !isset(
                $datos['version'],
                $datos['salt'],
                $datos['nonce'],
                $datos['ciphertext']
            )
        ) {
            throw new RuntimeException(
                'El formato de la clave cifrada no es válido.'
            );
        }

        $salt = base64_decode($datos['salt'], true);
        $nonce = base64_decode($datos['nonce'], true);
        $cifrado = base64_decode($datos['ciphertext'], true);

        if (
            $salt === false ||
            $nonce === false ||
            $cifrado === false
        ) {
            throw new RuntimeException(
                'No fue posible interpretar la clave cifrada.'
            );
        }

        $claveDerivada = sodium_crypto_pwhash(
            SODIUM_CRYPTO_SECRETBOX_KEYBYTES,
            $password,
            $salt,
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_ALG_ARGON2ID13
        );

        $claveConversacion = sodium_crypto_secretbox_open(
            $cifrado,
            $nonce,
            $claveDerivada
        );

        sodium_memzero($claveDerivada);

        if ($claveConversacion === false) {
            throw new RuntimeException(
                'No fue posible desbloquear la conversación.'
            );
        }

        return $claveConversacion;
    }

    public function cifrarMensaje(
        string $mensaje,
        string $claveConversacion
    ): string {
        $nonce = random_bytes(
            SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
        );

        $cifrado = sodium_crypto_secretbox(
            $mensaje,
            $nonce,
            $claveConversacion
        );

        return json_encode([
            'version' => self::VERSION,
            'nonce' => base64_encode($nonce),
            'ciphertext' => base64_encode($cifrado),
        ], JSON_THROW_ON_ERROR);
    }

    public function descifrarMensaje(
        string $payload,
        string $claveConversacion
    ): string {
        $datos = json_decode(
            $payload,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (
            !isset(
                $datos['version'],
                $datos['nonce'],
                $datos['ciphertext']
            )
        ) {
            throw new RuntimeException(
                'El formato del mensaje cifrado no es válido.'
            );
        }

        $nonce = base64_decode($datos['nonce'], true);
        $cifrado = base64_decode(
            $datos['ciphertext'],
            true
        );

        if ($nonce === false || $cifrado === false) {
            throw new RuntimeException(
                'No fue posible interpretar el mensaje cifrado.'
            );
        }

        $mensaje = sodium_crypto_secretbox_open(
            $cifrado,
            $nonce,
            $claveConversacion
        );

        if ($mensaje === false) {
            throw new RuntimeException(
                'No fue posible descifrar el mensaje.'
            );
        }

        return $mensaje;
    }
}
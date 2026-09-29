<?php

namespace App\Services;

use App\Models\Adjunto;
use App\Models\Mensaje;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AdjuntoService
{
    public function guardarImagen(
        UploadedFile $archivo,
        Mensaje $mensaje,
        string $claveConversacion
    ): Adjunto {
        $contenido = file_get_contents(
            $archivo->getRealPath()
        );

        if ($contenido === false) {
            throw new RuntimeException(
                'No fue posible leer la imagen.'
            );
        }

        $nonce = random_bytes(
            SODIUM_CRYPTO_SECRETBOX_NONCEBYTES
        );

        $contenidoCifrado = sodium_crypto_secretbox(
            $contenido,
            $nonce,
            $claveConversacion
        );

        $payload = json_encode([
            'version' => 1,
            'nonce' => base64_encode($nonce),
            'ciphertext' => base64_encode($contenidoCifrado),
        ], JSON_THROW_ON_ERROR);

        $nombreArchivo = Str::uuid()->toString() . '.enc';

        $ruta = 'anonymous-chat/' . $nombreArchivo;

        Storage::disk('local')->put(
            $ruta,
            $payload
        );

        return $mensaje->adjuntos()->create([
            'nombre_original_cifrado' =>
                Crypt::encryptString(
                    $archivo->getClientOriginalName()
                ),

            'ruta' => $ruta,

            'mime' =>
                $archivo->getMimeType()
                ?? 'application/octet-stream',

            'tamano' =>
                $archivo->getSize(),
        ]);
    }

    public function obtenerContenido(
        Adjunto $adjunto,
        string $claveConversacion
    ): string {
        $payload = Storage::disk('local')->get(
            $adjunto->ruta
        );

        $datos = json_decode(
            $payload,
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        if (
            !isset(
                $datos['nonce'],
                $datos['ciphertext']
            )
        ) {
            throw new RuntimeException(
                'El archivo cifrado no es válido.'
            );
        }

        $nonce = base64_decode(
            $datos['nonce'],
            true
        );

        $cifrado = base64_decode(
            $datos['ciphertext'],
            true
        );

        if (
            $nonce === false ||
            $cifrado === false
        ) {
            throw new RuntimeException(
                'No fue posible interpretar el archivo.'
            );
        }

        $contenido = sodium_crypto_secretbox_open(
            $cifrado,
            $nonce,
            $claveConversacion
        );

        if ($contenido === false) {
            throw new RuntimeException(
                'No fue posible descifrar el archivo.'
            );
        }

        return $contenido;
    }
}
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
    public function guardarImagen(UploadedFile $archivo, Mensaje $mensaje, string $claveConversacion): Adjunto 
    {
        $contenido = file_get_contents(
            $archivo->getRealPath()
        );

        $mime = $archivo->getMimeType();

        $mimesPermitidos = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ];

        if (
            !$mime ||
            !in_array(
                $mime,
                $mimesPermitidos,
                true
            )
        ) {
            throw new RuntimeException(
                'Tipo de imagen no permitido.'
            );
        }

        $infoImagen = @getimagesize($archivo->getRealPath());

        if ($infoImagen === false) {
            throw new RuntimeException(
                'El archivo no contiene una imagen válida.'
            );
        }

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

        $tamano = $archivo->getSize();

        if (
            $tamano === false ||
            $tamano <= 0 ||
            $tamano > 5 * 1024 * 1024
        ) {
            throw new RuntimeException(
                'El tamaño de la imagen no es válido.'
            );
        }

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

            'mime' => $mime,

            'tamano' => $tamano,
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
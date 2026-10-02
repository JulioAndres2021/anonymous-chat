<?php

namespace App\Services;

use App\Models\PurgaArchivo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class PurgaArchivoService
{
    public function procesar(PurgaArchivo $purga): bool 
    {
        if ($purga->purgado_at) {
            return true;
        }

        $purga->increment(
            'intentos'
        );

        try {
            $disk = Storage::disk(
                $purga->disk
            );

            /*
             * Si ya no existe, lo consideramos
             * purgado. Esto hace el proceso
             * idempotente.
             */
            if (!$disk->exists($purga->ruta)) {

                $purga->update([
                    'purgado_at' => now(),
                    'ultimo_error' => null,
                ]);

                return true;
            }

            $eliminado = $disk->delete(
                $purga->ruta
            );

            if (!$eliminado) {
                throw new RuntimeException(
                    'El filesystem no pudo eliminar el archivo.'
                );
            }

            $purga->update([
                'purgado_at' => now(),
                'ultimo_error' => null,
            ]);

            return true;

        } catch (Throwable $e) {

            $purga->update([
                'ultimo_error' =>
                    mb_substr(
                        $e->getMessage(),
                        0,
                        2000
                    ),
            ]);

            Log::error(
                'No fue posible purgar un archivo cifrado.',
                [
                    'purga_id' =>
                        $purga->id,

                    'disk' =>
                        $purga->disk,

                    'ruta' =>
                        $purga->ruta,
                ]
            );

            return false;
        }
    }
}

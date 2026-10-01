<?php

namespace App\Console\Commands;

use App\Models\PurgaArchivo;
use App\Services\PurgaArchivoService;
use Illuminate\Console\Command;

class PurgarArchivosPendientes extends Command
{
    protected $signature =
        'anonymous:purgar-archivos';

    protected $description =
        'Reintenta eliminar archivos cifrados pendientes de purga';

    public function handle(
        PurgaArchivoService $service
    ): int {
        $procesados = 0;
        $fallidos = 0;

        PurgaArchivo::query()
            ->whereNull(
                'purgado_at'
            )
            ->orderBy('id')
            ->chunkById(
                100,
                function ($purgas) use (
                    $service,
                    &$procesados,
                    &$fallidos
                ) {
                    foreach (
                        $purgas as $purga
                    ) {
                        if (
                            $service
                                ->procesar(
                                    $purga
                                )
                        ) {
                            $procesados++;
                        } else {
                            $fallidos++;
                        }
                    }
                }
            );

        $this->info(
            "Archivos purgados: {$procesados}"
        );

        if ($fallidos > 0) {
            $this->warn(
                "Archivos pendientes: {$fallidos}"
            );
        }

        return self::SUCCESS;
    }
}

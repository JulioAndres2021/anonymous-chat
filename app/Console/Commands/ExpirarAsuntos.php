<?php

namespace App\Console\Commands;

use App\Models\Asunto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirarAsuntos extends Command
{
    protected $signature = 'anonymous:expirar-asuntos';

    protected $description = 'Marca como eliminados los asuntos vencidos y sus datos relacionados';

    public function handle(): int
    {
        $cantidad = 0;

        Asunto::query()
            ->whereNotNull('expira_at')
            ->where('expira_at', '<=', now())
            ->chunkById(100, function ($asuntos) use (&$cantidad) {

                foreach ($asuntos as $asunto) {
                    DB::transaction(function () use ($asunto, &$cantidad) {

                        $asunto->mensajes()
                            ->with('adjuntos')
                            ->get()
                            ->each(function ($mensaje) {

                                $mensaje->adjuntos
                                    ->each(fn ($adjunto) => $adjunto->delete());

                                $mensaje->delete();
                            });

                        $asunto->delete();

                        $cantidad++;
                    });
                }
            });

        $this->info(
            "Asuntos expirados: {$cantidad}"
        );

        return self::SUCCESS;
    }
}

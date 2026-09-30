<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class VerificarBackups extends Command
{
    protected $signature =
        'anonymous:verificar-backups';

    protected $description =
        'Verifica directorios críticos de almacenamiento';

    public function handle(): int
    {
        $this->info(
            'Verificando almacenamiento...'
        );

        if (
            !Storage::disk('local')
                ->exists('anonymous-chat')
        ) {
            $this->warn(
                'No existe el directorio de adjuntos.'
            );
        } else {
            $this->info(
                '[ OK ] Directorio de adjuntos encontrado.'
            );
        }

        if (
            !Storage::disk('local')
                ->exists('../backups')
        ) {
            $this->warn(
                'Directorio de backups no disponible.'
            );
        }

        return self::SUCCESS;
    }
}
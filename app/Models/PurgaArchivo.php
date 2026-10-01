<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurgaArchivo extends Model
{
    protected $table =
        'purgas_archivos';

    protected $fillable = [
        'disk',
        'ruta',
        'intentos',
        'ultimo_error',
        'purgado_at',
    ];

    protected function casts(): array
    {
        return [
            'intentos' => 'integer',
            'purgado_at' => 'datetime',
        ];
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntentoAcceso extends Model
{
    protected $table = 'intentos_acceso';

    protected $fillable = [
        'asunto_id',
        'ip_cifrada',
        'ip_hash',
        'sistema_operativo',
        'navegador',
        'user_agent',
        'pais',
        'provincia',
        'ciudad',
        'intentos',
        'ultimo_intento_at',
        'bloqueado_hasta',
    ];

    protected function casts(): array
    {
        return [
            'ultimo_intento_at' => 'datetime',
            'bloqueado_hasta' => 'datetime',
        ];
    }

    public function asunto(): BelongsTo
    {
        return $this->belongsTo(Asunto::class);
    }
}

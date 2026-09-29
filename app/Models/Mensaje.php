<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mensaje extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'asunto_id',
        'contenido_cifrado',
        'ip_cifrada',
        'sistema_operativo',
        'navegador',
        'user_agent',
        'pais',
        'provincia',
        'ciudad',
    ];

    public function asunto(): BelongsTo
    {
        return $this->belongsTo(Asunto::class);
    }

    public function adjuntos(): HasMany
    {
        return $this->hasMany(Adjunto::class);
    }
    
}
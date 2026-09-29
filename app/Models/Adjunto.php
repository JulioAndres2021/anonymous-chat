<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Adjunto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'mensaje_id',
        'nombre_original_cifrado',
        'ruta',
        'mime',
        'tamano',
    ];

    public function mensaje(): BelongsTo
    {
        return $this->belongsTo(Mensaje::class);
    }
}

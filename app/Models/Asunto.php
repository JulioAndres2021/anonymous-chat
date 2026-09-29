<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asunto extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'titulo',
        'slug',
        'password_hash',
        'clave_password_cifrada',
        'clave_admin_cifrada',
        'expira_at',
    ];

    protected function casts(): array
    {
        return [
            'expira_at' => 'datetime',
        ];
    }
}

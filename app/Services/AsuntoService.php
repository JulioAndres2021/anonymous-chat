<?php

namespace App\Services;

use App\Models\Asunto;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AsuntoService
{
    public function crear(array $datos): Asunto
    {
        return Asunto::create([
            'titulo' => trim($datos['titulo']),
            'slug' => $this->generarSlugUnico($datos['titulo']),
            'password_hash' => Hash::make($datos['password']),

            /*
             * Temporalmente dejamos estas claves preparadas.
             * En la siguiente fase implementaremos el cifrado real.
             */
            'clave_password_cifrada' => '',
            'clave_admin_cifrada' => null,

            'expira_at' => $datos['expira_at'],
        ]);
    }

    private function generarSlugUnico(string $titulo): string
    {
        $slugBase = Str::slug($titulo);

        $slug = $slugBase;
        $contador = 2;

        while (Asunto::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        return $slug;
    }
}
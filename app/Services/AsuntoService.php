<?php

namespace App\Services;

use App\Models\Asunto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AsuntoService
{
    public function __construct(
        private readonly CifradoConversacionService $cifradoService
    ) {
    }

    public function crear(array $datos, array $cliente): Asunto
    {
        return DB::transaction(function () use ($datos) {

            $claveConversacion =
                $this->cifradoService->generarClaveConversacion();

            $claveProtegida =
                $this->cifradoService->protegerClaveConPassword(
                    $claveConversacion,
                    $datos['password']
                );

            $asunto = Asunto::create([
                'titulo' => trim($datos['titulo']),

                'slug' => $this->generarSlugUnico(
                    $datos['titulo']
                ),

                'password_hash' => Hash::make(
                    $datos['password']
                ),

                'clave_password_cifrada' => $claveProtegida,

                /*
                 * Más adelante la misma clave de conversación
                 * también quedará protegida para el administrador.
                 */
                'clave_admin_cifrada' => null,

                'expira_at' => $datos['expira_at'],
            ]);

            $contenidoCifrado =
                $this->cifradoService->cifrarMensaje(
                    $datos['mensaje'],
                    $claveConversacion
                );

            $asunto->mensajes()->create([
                'contenido_cifrado' =>
                    $contenidoCifrado,

                'ip_cifrada' =>
                    $cliente['ip_cifrada'] ?? null,

                'sistema_operativo' =>
                    $cliente['sistema_operativo'] ?? null,

                'navegador' =>
                    $cliente['navegador'] ?? null,

                'user_agent' =>
                    $cliente['user_agent'] ?? null,

                'pais' =>
                    $cliente['pais'] ?? null,

                'provincia' =>
                    $cliente['provincia'] ?? null,

                'ciudad' =>
                    $cliente['ciudad'] ?? null,
            ]);

            sodium_memzero($claveConversacion);

            return $asunto;
        });
    }

    private function generarSlugUnico(string $titulo): string
    {
        $slugBase = Str::slug($titulo);

        /*
         * Evitamos un slug vacío en títulos formados únicamente
         * por caracteres que Str::slug no pueda representar.
         */
        if ($slugBase === '') {
            $slugBase = 'asunto';
        }

        $slug = $slugBase;
        $contador = 2;

        while (
            Asunto::withTrashed()
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        return $slug;
    }
}

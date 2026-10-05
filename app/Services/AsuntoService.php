<?php

namespace App\Services;

use App\Models\Asunto;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AsuntoService
{
    public function __construct(
        private readonly CifradoConversacionService $cifradoService,
        private readonly AdjuntoService $adjuntoService
    ) {
    }

    public function crear(
        array $datos,
        array $cliente
    ): Asunto {
        return DB::transaction(
            function () use (
                $datos,
                $cliente
            ) {
                /*
                 * 1. Generamos una clave única
                 * para esta conversación.
                 */
                $claveConversacion =
                    $this->cifradoService
                        ->generarClaveConversacion();

                /*
                 * 2. Protegemos la clave
                 * con la contraseña del usuario.
                 */
                $claveProtegida =
                    $this->cifradoService
                        ->protegerClaveConPassword(
                            $claveConversacion,
                            $datos['password']
                        );

                /*
                 * 3. Protegemos también la clave
                 * para recuperación administrativa.
                 */
                $claveAdminCifrada =
                    $this->cifradoService
                        ->protegerClaveParaAdmin(
                            $claveConversacion
                        );

                /*
                 * 4. Primero creamos el asunto.
                 */
                $asunto = Asunto::create([
                    'titulo' =>
                        trim(
                            $datos['titulo']
                        ),

                    'slug' =>
                        $this->generarSlugUnico(
                            $datos['titulo']
                        ),

                    'password_hash' =>
                        Hash::make(
                            $datos['password']
                        ),

                    'clave_password_cifrada' =>
                        $claveProtegida,

                    'clave_admin_cifrada' =>
                        $claveAdminCifrada,

                    'expira_at' =>
                        $datos['expira_at'],
                ]);

                /*
                 * 5. Ciframos el mensaje inicial.
                 */
                $contenidoCifrado =
                    $this->cifradoService
                        ->cifrarMensaje(
                            $datos['mensaje'],
                            $claveConversacion
                        );

                /*
                 * 6. Creamos el mensaje inicial
                 * asociado al asunto.
                 */
                $mensaje =
                    $asunto
                        ->mensajes()
                        ->create([
                            'contenido_cifrado' =>
                                $contenidoCifrado,

                            'ip_cifrada' =>
                                $cliente[
                                    'ip_cifrada'
                                ] ?? null,

                            'sistema_operativo' =>
                                $cliente[
                                    'sistema_operativo'
                                ] ?? null,

                            'navegador' =>
                                $cliente[
                                    'navegador'
                                ] ?? null,

                            'user_agent' =>
                                $cliente[
                                    'user_agent'
                                ] ?? null,
                        ]);

                /*
                 * 7. Si el usuario adjuntó
                 * una imagen al crear el asunto,
                 * la ciframos con la misma clave
                 * de conversación.
                 */
                if (!empty($datos['imagen'])) {
                    $this->adjuntoService
                        ->guardarImagen(
                            $datos['imagen'],
                            $mensaje,
                            $claveConversacion
                        );
                }

                /*
                 * 8. Limpiamos la clave
                 * de memoria.
                 */
                sodium_memzero(
                    $claveConversacion
                );

                return $asunto;
            }
        );
    }

    private function generarSlugUnico(
        string $titulo
    ): string {
        $slugBase =
            Str::slug($titulo);

        if ($slugBase === '') {
            $slugBase = 'asunto';
        }

        $slug = $slugBase;
        $contador = 2;

        while (
            Asunto::withTrashed()
                ->where(
                    'slug',
                    $slug
                )
                ->exists()
        ) {
            $slug =
                $slugBase
                . '-'
                . $contador;

            $contador++;
        }

        return $slug;
    }
}
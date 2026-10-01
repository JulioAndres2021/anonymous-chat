<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asunto;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Services\CifradoConversacionService;
use Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurgaArchivo;
use App\Services\PurgaArchivoService;


class AsuntoController extends Controller
{
    public function __construct
    (
        private readonly CifradoConversacionService $cifradoService,
        private readonly PurgaArchivoService $purgaArchivoService
    ) {}

    public function index(): View
    {
        $asuntos = Asunto::query()
            ->withCount([
                'mensajes',
            ])
            ->latest()
            ->paginate(20);

        return view(
            'admin.asuntos.index',
            compact('asuntos')
        );
    }

    public function show(Asunto $asunto): View
    {
        $asunto->loadCount([
            'mensajes',
            'intentosAcceso',
        ]);

        $asunto->load([
            'mensajes.adjuntos',
            'intentosAcceso',
        ]);

        $mensajesDescifrados = collect();

        $errorDescifrado = null;

        if (!$asunto->clave_admin_cifrada) {

            $errorDescifrado =
                'Este asunto no tiene clave administrativa disponible.';

        } else {

            try {

                $claveConversacion =
                    $this->cifradoService
                        ->recuperarClaveComoAdmin(
                            $asunto->clave_admin_cifrada
                        );

                $mensajesDescifrados =
                    $asunto->mensajes
                        ->map(function ($mensaje) use ($claveConversacion) {

                            $mensaje->contenido_descifrado =
                                $this->cifradoService
                                    ->descifrarMensaje(
                                        $mensaje->contenido_cifrado,
                                        $claveConversacion
                                    );

                            return $mensaje;
                        });

                sodium_memzero(
                    $claveConversacion
                );

            } catch (Throwable $e) {

                $errorDescifrado =
                    'No fue posible descifrar la conversación.';
            }
        }

        return view(
            'admin.asuntos.show',
            compact(
                'asunto',
                'mensajesDescifrados',
                'errorDescifrado'
            )
        );
    }

    public function papelera(): View
    {
        $asuntos = Asunto::onlyTrashed()
            ->withCount([
                'mensajesConEliminados',
            ])
            ->latest('deleted_at')
            ->paginate(20);

        return view(
            'admin.asuntos.papelera',
            compact('asuntos')
        );
    }

    public function restaurar(int $id): RedirectResponse
    {
        $asunto = Asunto::onlyTrashed()
            ->findOrFail($id);

        $asunto->restore();

        if (
            $asunto->expira_at &&
            $asunto->expira_at->isPast()
        ) {
            $asunto->update([
                'expira_at' =>
                    now()->addDay(),
            ]);
        }


        $asunto->mensajes()
            ->onlyTrashed()
            ->restore();

        $asunto->mensajes()
            ->withTrashed()
            ->get()
            ->each(function ($mensaje) {
                $mensaje->adjuntos()
                    ->onlyTrashed()
                    ->restore();
            });

        return redirect()
            ->route('admin.papelera')
            ->with(
                'success',
                'Asunto restaurado correctamente.'
            );
    }

    public function eliminarDefinitivamente(int $id): RedirectResponse
    {
        $asunto = Asunto::onlyTrashed()
            ->findOrFail($id);

        $purgas = collect();

        DB::transaction(
            function () use (
                $asunto,
                &$purgas
            ) {
                $mensajes = $asunto
                    ->mensajes()
                    ->withTrashed()
                    ->with([
                        'adjuntos' =>
                            function ($query) {
                                $query
                                    ->withTrashed();
                            },
                    ])
                    ->get();

                foreach (
                    $mensajes as $mensaje
                ) {
                    foreach (
                        $mensaje->adjuntos
                        as $adjunto
                    ) {
                        if ($adjunto->ruta) {

                            $purga =
                                PurgaArchivo::
                                    firstOrCreate(
                                        [
                                            'disk' =>
                                                'local',

                                            'ruta' =>
                                                $adjunto
                                                    ->ruta,
                                        ]
                                    );

                            $purgas->push(
                                $purga
                            );
                        }

                        $adjunto
                            ->forceDelete();
                    }

                    $mensaje
                        ->forceDelete();
                }

                /*
                * Estos registros ya no tienen
                * utilidad una vez que el asunto
                * se elimina definitivamente.
                */
                $asunto
                    ->intentosAcceso()
                    ->delete();

                $asunto
                    ->forceDelete();
            }
        );

        /*
        * Esta parte ocurre SOLO después
        * del COMMIT de la BD.
        */
        $purgas
            ->unique('id')
            ->each(
                function ($purga) {
                    $this
                        ->purgaArchivoService
                        ->procesar(
                            $purga->fresh()
                        );
                }
            );

        return redirect()
            ->route('admin.papelera')
            ->with(
                'success',
                __(
                    'messages.thread_deleted_permanently'
                )
            );
    }


}

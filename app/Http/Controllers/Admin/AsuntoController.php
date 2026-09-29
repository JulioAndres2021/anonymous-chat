<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asunto;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AsuntoController extends Controller
{
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

    public function show(
        Asunto $asunto
    ): View {
        $asunto->loadCount([
            'mensajes',
            'intentosAcceso',
        ]);

        $asunto->load([
            'mensajes.adjuntos',
            'intentosAcceso',
        ]);

        return view(
            'admin.asuntos.show',
            compact('asunto')
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
}

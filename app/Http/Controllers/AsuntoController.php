<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearAsuntoRequest;
use App\Models\Asunto;
use App\Services\AsuntoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AsuntoController extends Controller
{
    public function __construct(
        private readonly AsuntoService $asuntoService
    ) {
    }

    public function index(): View
    {
        $asuntos = Asunto::query()
            ->where(function ($query) {
                $query
                    ->whereNull('expira_at')
                    ->orWhere('expira_at', '>', now());
            })
            ->latest()
            ->paginate(20);

        return view('asuntos.index', compact('asuntos'));
    }

    public function create(): View
    {
        return view('asuntos.create');
    }

    public function store(CrearAsuntoRequest $request): RedirectResponse
    {
        $asunto = $this->asuntoService->crear(
            $request->validated()
        );

        return redirect()
            ->route('asuntos.index')
            ->with(
                'success',
                "El asunto '{$asunto->titulo}' fue creado correctamente."
            );
    }
}

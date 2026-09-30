<?php

namespace App\Http\Controllers;

use App\Http\Requests\CrearAsuntoRequest;
use App\Models\Asunto;
use App\Services\AsuntoService;
use App\Services\InformacionClienteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $cliente = app(InformacionClienteService::class)->obtener($request);

        $asunto = $this->asuntoService->crear(
            $request->validated(),
            $cliente
        );

        return redirect()
            ->route('asuntos.index')
            ->with(
                'success',
                "El asunto '{$asunto->titulo}' fue creado correctamente."
            );
    }

    public function buscar(Request $request): JsonResponse
    {
        $busqueda = trim(
            (string) $request->query('q', '')
        );

        $asuntos = Asunto::query()
            ->where(function ($query) {
                $query
                    ->whereNull('expira_at')
                    ->orWhere(
                        'expira_at',
                        '>',
                        now()
                    );
            })
            ->when(
                $busqueda !== '',
                function ($query) use ($busqueda) {
                    $query->where(
                        'titulo',
                        'like',
                        '%' . $busqueda . '%'
                    );
                }
            )
            ->latest()
            ->limit(20)
            ->get([
                'id',
                'titulo',
                'slug',
                'expira_at',
            ]);

        return response()->json(
            $asuntos->map(
                function ($asunto) {
                    return [
                        'titulo' =>
                            $asunto->titulo,

                        'slug' =>
                            $asunto->slug,

                        'url' =>
                            route(
                                'asuntos.acceso',
                                $asunto
                            ),

                        'expira' =>
                            optional(
                                $asunto->expira_at
                            )->format(
                                'd/m/Y H:i'
                            ),
                    ];
                }
            )
        );
    }

    public function sugerencias(Request $request): JsonResponse 
    {
        $titulo = trim(
            (string) $request->query(
                'titulo',
                ''
            )
        );

        if ($titulo === '') {
            return response()->json([
                'existe' => false,
                'sugerencias' => [],
            ]);
        }

        $existe = Asunto::withTrashed()
            ->whereRaw(
                'LOWER(titulo) = ?',
                [
                    mb_strtolower($titulo)
                ]
            )
            ->exists();

        if (!$existe) {
            return response()->json([
                'existe' => false,
                'sugerencias' => [],
            ]);
        }

        $base = trim($titulo);

        $candidatos = [
            $base . '-2',
            $base . '-' . random_int(10, 99),
            'mi-' . $base,
            $base . '-chat',
        ];

        $sugerencias = [];

        foreach ($candidatos as $candidato) {

            $ocupado = Asunto::withTrashed()
                ->whereRaw(
                    'LOWER(titulo) = ?',
                    [
                        mb_strtolower(
                            $candidato
                        )
                    ]
                )
                ->exists();

            if (!$ocupado) {
                $sugerencias[] =
                    $candidato;
            }

            if (
                count($sugerencias) >= 3
            ) {
                break;
            }
        }

        return response()->json([
            'existe' => true,
            'sugerencias' =>
                $sugerencias,
        ]);
    }
}

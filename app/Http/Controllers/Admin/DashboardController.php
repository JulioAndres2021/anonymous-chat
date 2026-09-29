<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Adjunto;
use App\Models\Asunto;
use App\Models\IntentoAcceso;
use App\Models\Mensaje;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $estadisticas = [
            'asuntos_activos' =>
                Asunto::count(),

            'asuntos_eliminados' =>
                Asunto::onlyTrashed()->count(),

            'mensajes' =>
                Mensaje::withTrashed()->count(),

            'adjuntos' =>
                Adjunto::withTrashed()->count(),

            'intentos_fallidos' =>
                IntentoAcceso::where(
                    'intentos',
                    '>',
                    0
                )->count(),
        ];

        return view(
            'admin.dashboard',
            compact('estadisticas')
        );
    }
}

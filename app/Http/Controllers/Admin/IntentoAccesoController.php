<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IntentoAcceso;
use Illuminate\Support\Facades\Crypt;
use Illuminate\View\View;
use Throwable;

class IntentoAccesoController extends Controller
{
    public function index(): View
    {
        $intentos = IntentoAcceso::query()
            ->with([
                'asunto' => function ($query) {
                    $query->withTrashed();
                },
            ])
            ->where('intentos', '>', 0)
            ->latest('ultimo_intento_at')
            ->paginate(30);

        $intentos->getCollection()
            ->transform(function ($intento) {

                try {
                    $intento->ip_descifrada =
                        $intento->ip_cifrada
                            ? Crypt::decryptString(
                                $intento->ip_cifrada
                            )
                            : null;
                } catch (Throwable) {
                    $intento->ip_descifrada =
                        'No disponible';
                }

                return $intento;
            });

        return view(
            'admin.intentos.index',
            compact('intentos')
        );
    }
}

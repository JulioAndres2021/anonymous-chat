<?php

use App\Http\Controllers\AsuntoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccesoAsuntoController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\AdjuntoController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AsuntoController as AdminAsuntoController;
use App\Http\Controllers\Admin\IntentoAccesoController as AdminIntentoAccesoController;
use App\Http\Controllers\Admin\AdjuntoController as AdminAdjuntoController;

Route::get('/', [AsuntoController::class, 'index'])->name('asuntos.index');

//Ruta para cambiar IDIOMAS
Route::get(
    '/idioma/{locale}',
    function (string $locale) {

        if (!in_array(
            $locale,
            ['es', 'en'],
            true
        )) {
            abort(404);
        }

        session([
            'locale' => $locale,
        ]);

        return back();
    }
)->name('locale.change');

Route::get('/crear', [AsuntoController::class, 'create'])->name('asuntos.create');

Route::get(
    '/buscar-asuntos',
    [AsuntoController::class, 'buscar']
)
    ->middleware('throttle:buscar-asuntos')
    ->name('asuntos.buscar');

Route::post(
    '/asuntos',
    [AsuntoController::class, 'store']
)
    ->middleware('throttle:crear-asunto')
    ->name('asuntos.store');

Route::get('/a/{asunto}', [AccesoAsuntoController::class, 'show'])->name('asuntos.acceso');

Route::get('/a/{asunto}/chat', [AccesoAsuntoController::class, 'chat'])->middleware('asunto.desbloqueado')->name('asuntos.chat');

Route::post('/a/{asunto}/lock', [AccesoAsuntoController::class, 'lock'])->middleware('asunto.desbloqueado')->name('asuntos.lock');

Route::post(
    '/a/{asunto}/unlock',
    [AccesoAsuntoController::class, 'unlock']
)
    ->middleware('throttle:desbloquear-asunto')
    ->name('asuntos.unlock');


Route::post('/a/{asunto}/mensajes', [MensajeController::class, 'store'])
    ->middleware([
        'asunto.desbloqueado',
        'throttle:enviar-mensaje',
    ])->name('mensajes.store');

Route::get('/a/{asunto}/adjuntos/{adjunto}', [AdjuntoController::class, 'show'])
    ->middleware([
        'asunto.desbloqueado',
        'throttle:adjuntos',
    ])
    ->name('adjuntos.show');

    Route::get(
    '/asuntos/sugerencias',
    [AsuntoController::class, 'sugerencias']
)
    ->middleware('throttle:sugerencias-asuntos')
    ->name('asuntos.sugerencias');

    Route::prefix('admin')->name('admin.')->group(function () {

        Route::middleware('guest')
            ->group(function () {

                Route::get(
                    '/login',
                    [AuthController::class, 'showLogin']
                )
                    ->name('login');

                Route::post(
                    '/login',
                    [AuthController::class, 'login']
                )
                    ->middleware('throttle:admin-login')
                    ->name('login.store');
        });

        Route::middleware('auth')
            ->group(function () {

                Route::get(
                    '/',
                    [DashboardController::class, 'index']
                )
                    ->name('dashboard');

                Route::post(
                    '/logout',
                    [AuthController::class, 'logout']
                )
                    ->name('logout');

                Route::get(
                        '/asuntos',
                        [AdminAsuntoController::class, 'index']
                )
                    ->name('asuntos.index');

                Route::get(
                        '/asuntos/{asunto}',
                        [AdminAsuntoController::class, 'show']
                )
                    ->name('asuntos.show');

                Route::get(
                        '/papelera',
                        [AdminAsuntoController::class, 'papelera']
                )
                    ->name('papelera');

                Route::post(
                        '/papelera/{id}/restaurar',
                        [AdminAsuntoController::class, 'restaurar']
                )
                    ->name('papelera.restaurar');

                Route::get(
                        '/intentos',
                        [AdminIntentoAccesoController::class, 'index']
                )
                    ->name('intentos.index');

                Route::get('/adjuntos/{adjunto}', [AdminAdjuntoController::class, 'show'])
                    ->middleware('throttle:adjuntos')
                    ->name('adjuntos.show');

                Route::delete('/papelera/{id}', [AdminAsuntoController::class, 'eliminarDefinitivamente'])->name('papelera.destroy');

        });
    });

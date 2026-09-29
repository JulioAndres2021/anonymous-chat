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


Route::get('/', [AsuntoController::class, 'index'])->name('asuntos.index');

Route::get('/crear', [AsuntoController::class, 'create'])->name('asuntos.create');

Route::post('/asuntos', [AsuntoController::class, 'store'])->name('asuntos.store');

Route::get('/a/{asunto}', [AccesoAsuntoController::class, 'show'])->name('asuntos.acceso');

Route::post('/a/{asunto}/unlock', [AccesoAsuntoController::class, 'unlock'])->name('asuntos.unlock');

Route::get('/a/{asunto}/chat', [AccesoAsuntoController::class, 'chat'])
    ->middleware('asunto.desbloqueado')
    ->name('asuntos.chat');

Route::post('/a/{asunto}/mensajes', [MensajeController::class, 'store'])
    ->middleware('asunto.desbloqueado')
    ->name('mensajes.store');

Route::get('/a/{asunto}/adjuntos/{adjunto}', [AdjuntoController::class, 'show'])
    ->middleware('asunto.desbloqueado')
    ->name('adjuntos.show');

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

        });
    });

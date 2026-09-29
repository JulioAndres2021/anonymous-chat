<?php

use App\Http\Controllers\AsuntoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccesoAsuntoController;

Route::get('/', [AsuntoController::class, 'index'])->name('asuntos.index');

Route::get('/crear', [AsuntoController::class, 'create'])->name('asuntos.create');

Route::post('/asuntos', [AsuntoController::class, 'store'])->name('asuntos.store');

Route::get('/a/{asunto}', [AccesoAsuntoController::class, 'show'])->name('asuntos.acceso');

Route::post('/a/{asunto}/unlock', [AccesoAsuntoController::class, 'unlock'])->name('asuntos.unlock');

Route::get('/a/{asunto}/chat', [AccesoAsuntoController::class, 'chat'])
    ->middleware('asunto.desbloqueado')
    ->name('asuntos.chat');

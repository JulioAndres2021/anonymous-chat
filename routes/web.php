<?php

use App\Http\Controllers\AsuntoController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AsuntoController::class, 'index'])
    ->name('asuntos.index');

Route::get('/crear', [AsuntoController::class, 'create'])
    ->name('asuntos.create');

Route::post('/asuntos', [AsuntoController::class, 'store'])
    ->name('asuntos.store');

@extends('layouts.terminal')

@section('title', 'Admin Dashboard')

@section('terminal-title')
root@anonymous-chat:/admin# dashboard
@endsection

@section('prompt')
root@anonymous-chat:/admin#
@endsection

@section('content')

<p>
    <span class="prompt">
        root@anonymous-chat:/admin#
    </span>

    <span class="command">
        status
    </span>
</p>

<p class="success">
    authenticated:
    {{ auth()->user()->name }}
</p>

<div class="stats">

    <div class="stat">
        <div class="stat-value">
            {{ $estadisticas['asuntos_activos'] }}
        </div>

        <div class="stat-label">
            ACTIVE THREADS
        </div>
    </div>

    <div class="stat">
        <div class="stat-value">
            {{ $estadisticas['asuntos_eliminados'] }}
        </div>

        <div class="stat-label">
            TRASHED THREADS
        </div>
    </div>

    <div class="stat">
        <div class="stat-value">
            {{ $estadisticas['mensajes'] }}
        </div>

        <div class="stat-label">
            MESSAGES
        </div>
    </div>

    <div class="stat">
        <div class="stat-value">
            {{ $estadisticas['adjuntos'] }}
        </div>

        <div class="stat-label">
            ATTACHMENTS
        </div>
    </div>

    <div class="stat">
        <div class="stat-value">
            {{ $estadisticas['intentos_fallidos'] }}
        </div>

        <div class="stat-label">
            FAILED ACCESS
        </div>
    </div>

</div>

<div class="line"></div>

<p>
    <span class="prompt">
        root@anonymous-chat:/admin#
    </span>

    <span class="command">
        ls modules
    </span>
</p>

<ul class="terminal-list">

    <li>
        <a href="{{ route(
            'admin.asuntos.index'
        ) }}">
            active_threads
        </a>
    </li>

    <li>
        <a href="{{ route(
            'admin.papelera'
        ) }}">
            trash
        </a>
    </li>

    <li>
        <a href="{{ route(
            'admin.intentos.index'
        ) }}">
            failed_access
        </a>
    </li>

</ul>

<div class="line"></div>



@endsection
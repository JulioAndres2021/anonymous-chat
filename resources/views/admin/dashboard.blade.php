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
    <span class="prompt">root@anonymous-chat:/admin#</span>
    <span class="command">status</span>
</p>

<p class="success">
    {{ __('messages.authenticated') }}: {{ auth()->user()->name }}
</p>

<div class="stats">
    <div class="stat">
        <div class="stat-value">{{ $estadisticas['asuntos_activos'] }}</div>
        <div class="stat-label">{{ __('messages.active_threads') }}</div>
    </div>

    <div class="stat">
        <div class="stat-value">{{ $estadisticas['asuntos_eliminados'] }}</div>
        <div class="stat-label">{{ __('messages.trashed_threads') }}</div>
    </div>

    <div class="stat">
        <div class="stat-value">{{ $estadisticas['mensajes'] }}</div>
        <div class="stat-label">{{ __('messages.messages') }}</div>
    </div>

    <div class="stat">
        <div class="stat-value">{{ $estadisticas['adjuntos'] }}</div>
        <div class="stat-label">{{ __('messages.attachments') }}</div>
    </div>

    <div class="stat">
        <div class="stat-value">{{ $estadisticas['intentos_fallidos'] }}</div>
        <div class="stat-label">{{ __('messages.failed_access') }}</div>
    </div>
</div>

<div class="line"></div>

<p>
    <span class="prompt">root@anonymous-chat:/admin#</span>
    <span class="command">ls modules</span>
</p>

<ul class="terminal-list">
    <li><a href="{{ route('admin.asuntos.index') }}">{{ __('messages.admin_threads') }}</a></li>
    <li><a href="{{ route('admin.papelera') }}">{{ __('messages.admin_trash') }}</a></li>
    <li><a href="{{ route('admin.intentos.index') }}">{{ __('messages.failed_access') }}</a></li>
</ul>

<div class="line"></div>

@endsection

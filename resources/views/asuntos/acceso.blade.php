@extends('layouts.terminal')

@section('title', $asunto->titulo)

@section('terminal-title')
anonymous@chat:~/{{ $asunto->slug }}$
@endsection

@section('content')

<p>
    <span class="prompt">anonymous@chat:~/{{ $asunto->slug }}$</span>
    <span class="command">unlock</span>
</p>

<div class="line"></div>

<p>{{ __('messages.thread') }}: <strong>{{ $asunto->titulo }}</strong></p>

<p class="warning">[ {{ __('messages.locked') }} ] {{ __('messages.password_required') }}</p>

<p>
    {{ __('messages.status') }}=
    <span class="success">{{ __('messages.active') }}</span>
</p>

@if ($asunto->expira_at)
    <p class="muted">expires_at={{ $asunto->expira_at->format('d/m/Y H:i:s') }}</p>
    <p class="warning">{{ __('messages.remaining') }}={{ $asunto->expira_at->diffForHumans() }}</p>
@endif

<p class="muted">{{ __('messages.unlock_help') }}</p>

<form method="POST" action="{{ route('asuntos.unlock', $asunto) }}">
    @csrf

    <div class="field">
        <label for="password">{{ __('messages.password') }}:</label>
        <input id="password" type="password" name="password" autofocus>
    </div>

    <button type="submit">{{ __('messages.unlock') }}</button>
</form>

<div class="line"></div>

<a href="{{ route('asuntos.index') }}">← cd ..</a>

@endsection

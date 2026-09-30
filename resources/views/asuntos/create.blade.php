@extends('layouts.terminal')

@section('title', 'Crear asunto')

@section('terminal-title')
anonymous@chat:~$ new-thread
@endsection

@section('content')

<p>
    <span class="prompt">
        anonymous@chat:~$
    </span>

    <span class="command">
        create --anonymous
    </span>
</p>

<div class="line"></div>

<form
    action="{{ route('asuntos.store') }}"
    method="POST"
>
    @csrf

    <div class="field">
        <label for="titulo">
            asunto:
        </label>

        <input
            id="titulo"
            type="text"
            name="titulo"
            value="{{ old('titulo') }}"
            autocomplete="off"
            autofocus
        >
    </div>

    <div class="field">
        <label for="password">
            password:
        </label>

        <input
            id="password"
            type="password"
            name="password"
            autocomplete="new-password"
        >
    </div>

    <div class="field">
        <label for="password_confirmation">
            repeat_password:
        </label>

        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
        >
    </div>

    <div class="field">
        <label for="expira_at">
            expires_at:
        </label>

        <input
            id="expira_at"
            type="datetime-local"
            name="expira_at"
            value="{{ old('expira_at') }}"
        >
    </div>

    <div class="field">
        <label for="mensaje">
            message:
        </label>

        <textarea
            id="mensaje"
            name="mensaje"
            rows="9"
        >{{ old('mensaje') }}</textarea>
    </div>

    <button type="submit">
        ./create-thread
    </button>

</form>

<div class="line"></div>

<a href="{{ route('asuntos.index') }}">
    ← volver
</a>

@endsection

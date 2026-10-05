@extends('layouts.terminal')

@section('title', __('messages.create_thread'))

@section('terminal-title')
anonymous@chat:~$ new-thread
@endsection

@section('content')

<div
    data-thread-suggestions
    data-suggestions-url="{{ route('asuntos.sugerencias') }}"
    data-available-text="{{ __('messages.title_available') }}"
    data-exists-text="{{ __('messages.title_exists') }}"
    data-suggestions-text="{{ __('messages.suggestions') }}"
    data-selected-text="{{ __('messages.selected') }}"
>

<p>
    <span class="prompt">anonymous@chat:~$</span>
    <span class="command">create --anonymous</span>
</p>

<div class="line"></div>

<div class="message">
    <p class="success">[ {{ __('messages.new_thread') }} ]</p>
    <p class="muted">{{ __('messages.new_thread_help') }}</p>
    <p class="muted">{{ __('messages.new_thread_password_help') }}</p>
    <p class="muted">{{ __('messages.new_thread_encryption_help') }}</p>
    <p class="warning">{{ __('messages.new_thread_expiration_help') }}</p>
</div>

<div class="line"></div>

<form
    action="{{ route('asuntos.store') }}"
    method="POST"
    enctype="multipart/form-data"
>
    @csrf

    <div class="field">
        <label for="titulo">
            {{ __('messages.subject') }}:
        </label>

        <input
            id="titulo"
            type="text"
            name="titulo"
            value="{{ old('titulo') }}"
            autocomplete="off"
            autofocus
        >

        <div
            id="titulo-sugerencias"
            class="suggestion-box"
        ></div>
    </div>

    <div class="field">
        <label for="password">
            {{ __('messages.password') }}:
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
            {{ __('messages.repeat_password') }}:
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
            {{ __('messages.expires_at') }}:
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
            {{ __('messages.message') }}:
        </label>

        <textarea
            id="mensaje"
            name="mensaje"
            rows="9"
        >{{ old('mensaje') }}</textarea>
    </div>

    <div class="field">
        <label for="imagen">
            {{ __('messages.attach_image') }}:
        </label>

        <input
            id="imagen"
            type="file"
            name="imagen"
            accept=".jpg,.jpeg,.png,.webp,.gif,image/jpeg,image/png,image/webp,image/gif"
        >

        <p class="muted">
            JPG, PNG, WEBP o GIF · máximo 5 MB
        </p>
    </div>

    <button type="submit">
        ./{{ __('messages.create') }}
    </button>
</form>

<div class="line"></div>

<a href="{{ route('asuntos.index') }}">
    ← {{ __('messages.back') }}
</a>

</div>

@endsection
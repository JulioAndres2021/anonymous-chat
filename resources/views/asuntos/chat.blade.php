@extends('layouts.terminal')

@section('title', $asunto->titulo)

@section('terminal-title')
anonymous@chat:~/{{ $asunto->slug }}$
@endsection

@section('content')

<p>
    <span class="prompt">anonymous@chat:~/{{ $asunto->slug }}$</span>
    <span class="command">cat conversation.log</span>
</p>

<form
    method="POST"
    action="{{ route('asuntos.lock', $asunto) }}"
    class="lock-thread-form"
>
    @csrf
    <button type="submit" class="lock-thread-button">
        [ {{ __('messages.lock_thread') }} ]
    </button>
</form>

<p class="success">[ {{ __('messages.unlocked') }} ]</p>

<p>
    {{ __('messages.status') }}=
    <span class="success">{{ __('messages.active') }}</span>
</p>

@if ($asunto->expira_at)
    <p class="muted">expires_at={{ $asunto->expira_at->format('d/m/Y H:i:s') }}</p>
    <p class="warning">{{ __('messages.remaining') }}={{ $asunto->expira_at->diffForHumans() }}</p>
@endif

<p class="muted">{{ __('messages.chat_help') }}</p>
<p class="muted">{{ __('messages.chat_encryption_help') }}</p>

<div class="line"></div>

@forelse ($mensajes as $mensaje)
    <article class="message">
        <div class="message-content">{{ $mensaje->contenido_descifrado }}</div>

        @if ($mensaje->adjuntos->isNotEmpty())
            @foreach ($mensaje->adjuntos as $adjunto)
                <div class="attachment-link">
                    <a
                        href="{{ route('adjuntos.show', [$asunto, $adjunto]) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        [ {{ __('messages.image') }} ]
                    </a>
                </div>
            @endforeach
        @endif

        <div class="message-meta">
            timestamp: {{ $mensaje->created_at->format('d/m/Y H:i:s') }}
        </div>
    </article>

    <div class="line"></div>
@empty
    <p class="muted">{{ __('messages.empty_conversation') }}</p>
@endforelse

<p>
    <span class="prompt">anonymous@chat:~/{{ $asunto->slug }}$</span>
    <span class="command">reply</span>
</p>

<form
    method="POST"
    action="{{ route('mensajes.store', $asunto) }}"
    enctype="multipart/form-data"
>
    @csrf

    <div class="field">
        <label for="mensaje">{{ __('messages.reply') }}:</label>
        <textarea id="mensaje" name="mensaje" rows="7">{{ old('mensaje') }}</textarea>
    </div>

    <div class="field">
        <label for="imagen">{{ __('messages.attach_image') }}:</label>
        <input
            id="imagen"
            type="file"
            name="imagen"
            accept="image/jpeg,image/png,image/webp,image/gif"
        >
    </div>

    <button type="submit">./{{ __('messages.send') }}</button>
</form>

<div class="line"></div>

<a href="{{ route('asuntos.index') }}">← cd /asuntos</a>

@endsection

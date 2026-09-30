@extends('layouts.terminal')

@section('title', $asunto->titulo)

@section('terminal-title')
anonymous@chat:~/{{ $asunto->slug }}$
@endsection

@section('content')

<p>
    <span class="prompt">
        anonymous@chat:~/{{ $asunto->slug }}$
    </span>

    <span class="command">
        cat conversation.log
    </span>
</p>

<p class="success">
    [ UNLOCKED ]
</p>

<p class="muted">
    Podés leer la conversación, responder
    o adjuntar una imagen.
</p>

<p class="muted">
    Las respuestas se almacenan cifradas
    dentro de este asunto.
</p>

<div class="line"></div>

@forelse ($mensajes as $mensaje)

    <article class="message">

        <div class="message-content">
            {{ $mensaje->contenido_descifrado }}
        </div>

        @if ($mensaje->adjuntos->isNotEmpty())

            @foreach ($mensaje->adjuntos as $adjunto)

                <div style="margin-top: 10px;">

                    <a
                        href="{{ route(
                            'adjuntos.show',
                            [$asunto, $adjunto]
                        ) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        [ IMAGEN ]
                    </a>

                </div>

            @endforeach

        @endif

        <div class="message-meta">
            timestamp:
            {{ $mensaje->created_at->format(
                'd/m/Y H:i:s'
            ) }}
        </div>

    </article>

    <div class="line"></div>

@empty

    <p class="muted">
        conversation.log está vacío.
    </p>

@endforelse

<p>
    <span class="prompt">
        anonymous@chat:~/{{ $asunto->slug }}$
    </span>

    <span class="command">
        reply
    </span>
</p>

<form
    method="POST"
    action="{{ route(
        'mensajes.store',
        $asunto
    ) }}"
    enctype="multipart/form-data"
>
    @csrf

    <div class="field">

        <label for="mensaje">
            message:
        </label>

        <textarea
            id="mensaje"
            name="mensaje"
            rows="7"
        >{{ old('mensaje') }}</textarea>

    </div>

    <div class="field">

        <label for="imagen">
            attach_image:
        </label>

        <input
            id="imagen"
            type="file"
            name="imagen"
            accept="image/jpeg,image/png,image/webp,image/gif"
        >

    </div>

    <button type="submit">
        ./send
    </button>

</form>

<div class="line"></div>

<a href="{{ route('asuntos.index') }}">
    ← cd /asuntos
</a>

@endsection
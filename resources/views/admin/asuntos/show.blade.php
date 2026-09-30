@extends('layouts.terminal')

@section('title', $asunto->titulo)

@section('terminal-title')
root@anonymous-chat:/admin/asuntos/{{ $asunto->id }}#
@endsection

@section('prompt')
root@anonymous-chat:/admin/asuntos/{{ $asunto->id }}#
@endsection

@section('content')

<div class="nav">

    <a href="{{ route('admin.asuntos.index') }}">
        [ cd .. ]
    </a>

</div>

<p>
    <span class="prompt">
        root@anonymous-chat:/admin/asuntos/{{ $asunto->id }}#
    </span>

    <span class="command">
        inspect
    </span>
</p>

<div class="line"></div>

<p>
    id={{ $asunto->id }}
</p>

<p>
    title="{{ $asunto->titulo }}"
</p>

<p>
    slug={{ $asunto->slug }}
</p>

<p>
    created={{
        $asunto->created_at->format(
            'd/m/Y H:i:s'
        )
    }}
</p>

<p>
    expires={{
        optional(
            $asunto->expira_at
        )->format('d/m/Y H:i:s')
    }}
</p>
@if ($asunto->expira_at)

    <p>
        remaining={{
            $asunto->expira_at->diffForHumans()
        }}
    </p>

@endif
<p>
    messages={{ $asunto->mensajes_count }}
</p>

<p>
    access_records={{ $asunto->intentos_acceso_count }}
</p>

<div class="line"></div>

<p>
    <span class="prompt">
        root@anonymous-chat:/admin/asuntos/{{ $asunto->id }}#
    </span>

    <span class="command">
        cat conversation.log
    </span>
</p>

@if ($errorDescifrado)

    <p class="error">
        [ ERROR ]
        {{ $errorDescifrado }}
    </p>

@else

    @forelse ($mensajesDescifrados as $mensaje)

        <article class="message">

            <div class="message-content">
                {{ $mensaje->contenido_descifrado }}
            </div>

            @foreach ($mensaje->adjuntos as $adjunto)

                <div>
                    <a
                        href="{{ route(
                            'admin.adjuntos.show',
                            $adjunto
                        ) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        [ IMAGEN ]
                    </a>
                </div>

            @endforeach

            <div class="message-meta">

                id={{ $mensaje->id }}

                |
                time={{
                    $mensaje->created_at
                        ->format('d/m/Y H:i:s')
                }}

                |
                os={{
                    $mensaje->sistema_operativo
                    ?? 'unknown'
                }}

                |
                browser={{
                    $mensaje->navegador
                    ?? 'unknown'
                }}

            </div>

        </article>

        <div class="line"></div>

    @empty

        <p class="muted">
            conversation.log vacío.
        </p>

    @endforelse

@endif

@endsection
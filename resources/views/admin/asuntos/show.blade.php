@extends('layouts.terminal')

@section('title', $asunto->titulo)

@section('terminal-title')
root@anonymous-chat:/admin/asuntos/{{ $asunto->id }}#
@endsection

@section('prompt')
root@anonymous-chat:/admin/asuntos/{{ $asunto->id }}#
@endsection

@section('content')

<p>
    <span class="prompt">root@anonymous-chat:/admin/asuntos/{{ $asunto->id }}#</span>
    <span class="command">inspect</span>
</p>

<div class="line"></div>

<p>id={{ $asunto->id }}</p>
<p>{{ __('messages.title') }}="{{ $asunto->titulo }}"</p>
<p>slug={{ $asunto->slug }}</p>
<p>{{ __('messages.created') }}={{ $asunto->created_at->format('d/m/Y H:i:s') }}</p>
<p>{{ __('messages.expires') }}={{ optional($asunto->expira_at)->format('d/m/Y H:i:s') }}</p>

@if ($asunto->expira_at)
    <p>{{ __('messages.remaining') }}={{ $asunto->expira_at->diffForHumans() }}</p>
@endif

<p>messages={{ $asunto->mensajes_count }}</p>
<p>{{ __('messages.access_records') }}={{ $asunto->intentos_acceso_count }}</p>
<p>
    {{ __('messages.status') }}=
    <span class="success">{{ __('messages.active') }}</span>
</p>

<div class="line"></div>

<p>
    <span class="prompt">root@anonymous-chat:/admin/asuntos/{{ $asunto->id }}#</span>
    <span class="command">cat conversation.log</span>
</p>

@if ($errorDescifrado)
    <p class="error">[ {{ __('messages.error') }} ] {{ $errorDescifrado }}</p>
@else
    @forelse ($mensajesDescifrados as $mensaje)
        <article class="message">
            <div class="message-content">{{ $mensaje->contenido_descifrado }}</div>

            @foreach ($mensaje->adjuntos as $adjunto)
                <div>
                    <a
                        href="{{ route('admin.adjuntos.show', $adjunto) }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        [ {{ __('messages.image') }} ]
                    </a>
                </div>
            @endforeach

            <div class="message-meta">
                id={{ $mensaje->id }}
                | time={{ $mensaje->created_at->format('d/m/Y H:i:s') }}
                | os={{ $mensaje->sistema_operativo ?? __('messages.unknown') }}
                | browser={{ $mensaje->navegador ?? __('messages.unknown') }}
            </div>
        </article>

        <div class="line"></div>
    @empty
        <p class="muted">{{ __('messages.empty_conversation') }}</p>
    @endforelse
@endif

@endsection

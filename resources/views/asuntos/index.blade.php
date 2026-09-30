@extends('layouts.terminal')

@section('title', 'Anonymous Chat')

@section('terminal-title')
anonymous@chat:~$ ls asuntos
@endsection

@section('terminal-image')
<img src="{{ asset('images/anonymous-chat.png') }}" alt="Anonymous Chat">
@endsection

@section('content')

<div class="nav">
    <a href="{{ route('asuntos.create') }}" class="terminal-button">
        + {{ __('messages.create_thread') }}
    </a>
</div>

<div class="message">
    <p class="success">[ {{ __('messages.app_name') }} ]</p>
    <p>{{ __('messages.home_title') }}</p>
    <p class="muted">{{ __('messages.home_search_help') }}</p>
    <p class="muted">{{ __('messages.home_create_help') }}</p>
    <p class="warning">[ {{ __('messages.privacy') }} ] {{ __('messages.privacy_notice') }}</p>
    <p class="muted">{{ __('messages.password_warning') }}</p>
</div>

<div class="line"></div>

<div class="field">
    <label for="buscar-asunto">{{ __('messages.search') }}:</label>
    <input
        id="buscar-asunto"
        type="text"
        placeholder="{{ __('messages.search_placeholder') }}"
        autocomplete="off"
    >
</div>

<p id="search-status" class="muted">{{ __('messages.showing_threads') }}</p>

<div class="line"></div>

<p>
    <span class="prompt">anonymous@chat:~$</span>
    <span class="command">ls asuntos</span>
</p>

<div class="line"></div>

<ul id="lista-asuntos" class="terminal-list">
    @forelse ($asuntos as $asunto)
        <li>
            <a href="{{ route('asuntos.acceso', $asunto) }}">
                {{ $asunto->titulo }}
            </a>

            <span class="muted">
                · {{ __('messages.expires') }}
                {{ optional($asunto->expira_at)->format('d/m/Y H:i') }}
            </span>

            @if ($asunto->expira_at)
                <span class="warning">
                    · {{ __('messages.remaining') }}:
                    {{ $asunto->expira_at->diffForHumans() }}
                </span>
            @endif
        </li>
    @empty
        <li class="muted">{{ __('messages.no_threads') }}</li>
    @endforelse
</ul>

<div class="pagination">
    {{ $asuntos->links() }}
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const textos = {
        mostrando: @json(__('messages.showing_threads')),
        sinResultados: @json(__('messages.no_results')),
        resultados: @json(__('messages.results')),
        error: @json(__('messages.search_error')),
        expira: @json(__('messages.expires')),
    };

    const input = document.getElementById('buscar-asunto');
    const lista = document.getElementById('lista-asuntos');
    const estado = document.getElementById('search-status');
    const listadoOriginal = lista.innerHTML;

    let timeout = null;

    input.addEventListener('input', () => {
        clearTimeout(timeout);

        const valor = input.value.trim();

        if (valor === '') {
            lista.innerHTML = listadoOriginal;
            estado.textContent = textos.mostrando;
            return;
        }

        timeout = setTimeout(() => buscar(valor), 250);
    });

    async function buscar(query) {
        estado.textContent = `search "${query}"...`;

        try {
            const url = `{{ route('asuntos.buscar') }}?q=${encodeURIComponent(query)}`;

            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('search failed');
            }

            const asuntos = await response.json();
            lista.innerHTML = '';

            if (asuntos.length === 0) {
                const li = document.createElement('li');
                li.className = 'muted';
                li.textContent = textos.sinResultados;
                lista.appendChild(li);
                estado.textContent = `0 ${textos.resultados}`;
                return;
            }

            asuntos.forEach(asunto => {
                const li = document.createElement('li');
                const enlace = document.createElement('a');

                enlace.href = asunto.url;
                enlace.textContent = asunto.titulo;
                li.appendChild(enlace);

                if (asunto.expira) {
                    const meta = document.createElement('span');
                    meta.className = 'muted';
                    meta.textContent = ` · ${textos.expira} ${asunto.expira}`;
                    li.appendChild(meta);
                }

                lista.appendChild(li);
            });

            estado.textContent = `${asuntos.length} ${textos.resultados}`;
        } catch (error) {
            estado.textContent = textos.error;
        }
    }
});
</script>
@endpush

@extends('layouts.terminal')

@section('title', __('messages.create_thread'))

@section('terminal-title')
anonymous@chat:~$ new-thread
@endsection

@section('content')

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

<form action="{{ route('asuntos.store') }}" method="POST">
    @csrf

    <div class="field">
        <label for="titulo">{{ __('messages.subject') }}:</label>
        <input
            id="titulo"
            type="text"
            name="titulo"
            value="{{ old('titulo') }}"
            autocomplete="off"
            autofocus
        >
        <div id="titulo-sugerencias" style="margin-top:10px;"></div>
    </div>

    <div class="field">
        <label for="password">{{ __('messages.password') }}:</label>
        <input
            id="password"
            type="password"
            name="password"
            autocomplete="new-password"
        >
    </div>

    <div class="field">
        <label for="password_confirmation">{{ __('messages.repeat_password') }}:</label>
        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
        >
    </div>

    <div class="field">
        <label for="expira_at">{{ __('messages.expires_at') }}:</label>
        <input
            id="expira_at"
            type="datetime-local"
            name="expira_at"
            value="{{ old('expira_at') }}"
        >
    </div>

    <div class="field">
        <label for="mensaje">{{ __('messages.message') }}:</label>
        <textarea id="mensaje" name="mensaje" rows="9">{{ old('mensaje') }}</textarea>
    </div>

    <button type="submit">./{{ __('messages.create') }}</button>
</form>

<div class="line"></div>

<a href="{{ route('asuntos.index') }}">← {{ __('messages.back') }}</a>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const textos = {
        disponible: @json(__('messages.available')),
        nombreDisponible: @json(__('messages.name_available')),
        existe: @json(__('messages.exists')),
        asuntoExiste: @json(__('messages.thread_exists')),
        sugerencias: @json(__('messages.suggestions')),
        seleccionado: @json(__('messages.selected')),
    };

    const input = document.getElementById('titulo');
    const contenedor = document.getElementById('titulo-sugerencias');
    let timeout = null;

    input.addEventListener('input', () => {
        clearTimeout(timeout);
        timeout = setTimeout(comprobar, 400);
    });

    async function comprobar() {
        const titulo = input.value.trim();

        if (titulo.length < 3) {
            contenedor.innerHTML = '';
            return;
        }

        const url = `{{ route('asuntos.sugerencias') }}?titulo=${encodeURIComponent(titulo)}`;

        try {
            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('suggestion failed');
            }

            const data = await response.json();

            if (!data.existe) {
                contenedor.innerHTML = '';

                const span = document.createElement('span');
                span.className = 'success';
                span.textContent = `[ ${textos.disponible} ] ${textos.nombreDisponible}`;
                contenedor.appendChild(span);
                return;
            }

            contenedor.innerHTML = '';

            const aviso = document.createElement('div');
            aviso.className = 'warning';
            aviso.textContent = `[ ${textos.existe} ] ${textos.asuntoExiste}`;
            contenedor.appendChild(aviso);

            const tituloSugerencias = document.createElement('div');
            tituloSugerencias.className = 'muted';
            tituloSugerencias.textContent = `${textos.sugerencias}:`;
            contenedor.appendChild(tituloSugerencias);

            data.sugerencias.forEach(sugerencia => {
                const boton = document.createElement('button');
                boton.type = 'button';
                boton.className = 'titulo-sugerido';
                boton.textContent = sugerencia;
                boton.style.marginTop = '6px';
                boton.style.marginRight = '6px';

                boton.addEventListener('click', () => {
                    input.value = sugerencia;
                    contenedor.innerHTML = '';

                    const seleccionado = document.createElement('span');
                    seleccionado.className = 'success';
                    seleccionado.textContent = `[ ${textos.seleccionado} ] ${sugerencia}`;
                    contenedor.appendChild(seleccionado);
                });

                contenedor.appendChild(boton);
            });
        } catch (error) {
            contenedor.innerHTML = '';
        }
    }
});
</script>
@endpush

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
<div class="message">

    <p class="success">
        [ NEW THREAD ]
    </p>

    <p class="muted">
        Creá un asunto único para iniciar
        una conversación.
    </p>

    <p class="muted">
        La contraseña será necesaria para
        que otros usuarios puedan abrirla.
        No podrá recuperarse si la olvidás.
    </p>

    <p class="muted">
        El contenido de los mensajes y los
        archivos adjuntos se almacenan
        cifrados.
    </p>

    <p class="warning">
        Cuando llegue la fecha de expiración,
        el asunto dejará de estar disponible
        públicamente.
    </p>

</div>

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
        <div
            id="titulo-sugerencias"
            style="margin-top:10px;"
        ></div>
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

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', () => {

    const input =
        document.getElementById('titulo');

    const contenedor =
        document.getElementById(
            'titulo-sugerencias'
        );

    let timeout = null;

    input.addEventListener('input', () => {

        clearTimeout(timeout);

        timeout = setTimeout(
            comprobar,
            400
        );

    });

    async function comprobar() {

        const titulo =
            input.value.trim();

        if (titulo.length < 3) {
            contenedor.innerHTML = '';
            return;
        }

        const url =
            `{{ route('asuntos.sugerencias') }}` +
            `?titulo=${encodeURIComponent(titulo)}`;

        try {

            const response =
                await fetch(url, {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                });

            const data =
                await response.json();

            if (!data.existe) {

                contenedor.innerHTML = `
                    <span class="success">
                        [ AVAILABLE ]
                        Nombre disponible.
                    </span>
                `;

                return;
            }

            let html = `
                <div class="warning">
                    [ EXISTS ]
                    Ese asunto ya existe.
                </div>

                <div class="muted">
                    suggestions:
                </div>
            `;

            data.sugerencias.forEach(
                sugerencia => {

                    html += `
                        <button
                            type="button"
                            class="titulo-sugerido"
                            data-titulo="${escapar(
                                sugerencia
                            )}"
                            style="
                                margin-top:6px;
                                margin-right:6px;
                            "
                        >
                            ${escapar(
                                sugerencia
                            )}
                        </button>
                    `;
                }
            );

            contenedor.innerHTML = html;

            document
                .querySelectorAll(
                    '.titulo-sugerido'
                )
                .forEach(boton => {

                    boton.addEventListener(
                        'click',
                        () => {

                            input.value =
                                boton.dataset.titulo;

                            contenedor.innerHTML = `
                                <span class="success">
                                    [ SELECTED ]
                                    ${escapar(
                                        boton.dataset.titulo
                                    )}
                                </span>
                            `;
                        }
                    );

                });

        } catch {

            contenedor.innerHTML = '';
        }
    }

    function escapar(texto) {

        const div =
            document.createElement('div');

        div.textContent =
            texto;

        return div.innerHTML;
    }

});
</script>

@endpush
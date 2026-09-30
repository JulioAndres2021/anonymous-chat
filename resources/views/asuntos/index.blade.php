@extends('layouts.terminal')

@section('title', 'Anonymous Chat')

@section('terminal-title')
anonymous@chat:~$ ls asuntos
@endsection

@section('content')

<div class="nav">
    <a
        href="{{ route('asuntos.create') }}"
        class="terminal-button"
    >
        + CREAR ASUNTO
    </a>
</div>

<div class="message">

    <p class="success">
        [ ANONYMOUS-CHAT ]
    </p>

    <p>
        Sistema de conversaciones privadas
        sin registro de usuario.
    </p>

    <p class="muted">
        Podés buscar un asunto existente y,
        si conocés su contraseña, acceder a
        la conversación.
    </p>

    <p class="muted">
        También podés crear un nuevo asunto,
        definir una contraseña y una fecha
        de expiración, escribir mensajes y
        adjuntar imágenes.
    </p>

    <p class="warning">
        [ PRIVACY ]
        El sistema registra datos técnicos
        mínimos de seguridad, como IP,
        navegador y sistema operativo.
    </p>

    <p class="warning">
        No compartas la contraseña del asunto
        con personas que no deban acceder a
        la conversación.
    </p>

</div>

<div class="line"></div>

<div class="field">
    <label for="buscar-asunto">
        search:
    </label>

    <input
        id="buscar-asunto"
        type="text"
        placeholder="Escriba para buscar..."
        autocomplete="off"
    >
</div>

<p
    id="search-status"
    class="muted"
>
    Mostrando asuntos disponibles.
</p>

<div class="line"></div>

<p>
    <span class="prompt">
        anonymous@chat:~$
    </span>

    <span class="command">
        ls asuntos
    </span>
</p>

<div class="line"></div>

<ul
    id="lista-asuntos"
    class="terminal-list"
>

    @forelse ($asuntos as $asunto)

        <li>

            <a
                href="{{ route(
                    'asuntos.acceso',
                    $asunto
                ) }}"
            >
                {{ $asunto->titulo }}
            </a>

            <span class="muted">
                · expira
                {{ optional(
                    $asunto->expira_at
                )->format('d/m/Y H:i') }}
            </span>

            @if ($asunto->expira_at)

                <span class="warning">
                    · restante:
                    {{ $asunto->expira_at->diffForHumans() }}
                </span>

            @endif

        </li>

    @empty

        <li class="muted">
            No hay asuntos disponibles.
        </li>

    @endforelse

</ul>

<div class="pagination">
    {{ $asuntos->links() }}
</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', () => {

    const input =
        document.getElementById('buscar-asunto');

    const lista =
        document.getElementById('lista-asuntos');

    const estado =
        document.getElementById('search-status');

    const listadoOriginal =
        lista.innerHTML;

    let timeout = null;

    input.addEventListener('input', () => {

        clearTimeout(timeout);

        const valor =
            input.value.trim();

        if (valor === '') {

            lista.innerHTML =
                listadoOriginal;

            estado.textContent =
                'Mostrando asuntos disponibles.';

            return;
        }

        timeout = setTimeout(
            () => buscar(valor),
            250
        );
    });

    async function buscar(query) {

        estado.textContent =
            `search "${query}"...`;

        try {

            const url =
                `{{ route('asuntos.buscar') }}` +
                `?q=${encodeURIComponent(query)}`;

            const response =
                await fetch(url, {
                    headers: {
                        'Accept':
                            'application/json'
                    }
                });

            if (!response.ok) {
                throw new Error(
                    'Error en la búsqueda'
                );
            }

            const asuntos =
                await response.json();

            lista.innerHTML = '';

            if (asuntos.length === 0) {

                lista.innerHTML = `
                    <li class="muted">
                        No se encontraron asuntos.
                    </li>
                `;

                estado.textContent =
                    '0 resultados';

                return;
            }

            asuntos.forEach(asunto => {

                const li =
                    document.createElement('li');

                const enlace =
                    document.createElement('a');

                enlace.href =
                    asunto.url;

                enlace.textContent =
                    asunto.titulo;

                li.appendChild(enlace);

                if (asunto.expira) {

                    const meta =
                        document.createElement(
                            'span'
                        );

                    meta.className =
                        'muted';

                    meta.textContent =
                        ` · expira ${asunto.expira}`;

                    li.appendChild(meta);
                }

                lista.appendChild(li);
            });

            estado.textContent =
                `${asuntos.length} resultado(s)`;

        } catch (error) {

            estado.textContent =
                'No fue posible realizar la búsqueda.';
        }
    }

});
</script>

@endpush
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

<p>
    <span class="prompt">
        anonymous@chat:~$
    </span>

    <span class="command">
        ls asuntos
    </span>
</p>

<div class="line"></div>

<ul class="terminal-list">

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
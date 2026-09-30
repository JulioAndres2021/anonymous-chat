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
        unlock
    </span>
</p>

<div class="line"></div>

<p>
    THREAD:
    <strong>
        {{ $asunto->titulo }}
    </strong>
</p>

<p class="warning">
    [ LOCKED ] password required
</p>

<p>
    status=
    <span class="success">
        ACTIVE
    </span>
</p>

@if ($asunto->expira_at)

    <p class="muted">
        expires_at={{
            $asunto->expira_at
                ->format('d/m/Y H:i:s')
        }}
    </p>

    <p class="warning">
        remaining={{
            $asunto->expira_at
                ->diffForHumans()
        }}
    </p>

@endif


<p class="muted">
    Para leer o responder esta conversación
    necesitás la contraseña definida por
    quien creó el asunto.
</p>
<form
    method="POST"
    action="{{ route(
        'asuntos.unlock',
        $asunto
    ) }}"
>
    @csrf

    <div class="field">

        <label for="password">
            password:
        </label>

        <input
            id="password"
            type="password"
            name="password"
            autofocus
        >

    </div>

    <button type="submit">
        unlock
    </button>

</form>

<div class="line"></div>

<a href="{{ route('asuntos.index') }}">
    ← cd ..
</a>

@endsection

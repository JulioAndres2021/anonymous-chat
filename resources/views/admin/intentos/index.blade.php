@extends('layouts.terminal')

@section('title', 'Admin - Failed Access')

@section('terminal-title')
root@anonymous-chat:/admin/security#
@endsection

@section('prompt')
root@anonymous-chat:/admin/security#
@endsection

@section('content')

<div class="nav">

    <a href="{{ route('admin.dashboard') }}">
        [ dashboard ]
    </a>

    <a href="{{ route('admin.asuntos.index') }}">
        [ active_threads ]
    </a>

</div>

<p>
    <span class="prompt">
        root@anonymous-chat:/admin/security#
    </span>

    <span class="command">
        cat failed_access.log
    </span>
</p>

<div class="line"></div>

@forelse ($intentos as $intento)

    <article class="message">

        <div>
            thread={{
                $intento->asunto?->titulo
                ?? 'unknown'
            }}
        </div>

        <div>
            ip={{
                $intento->ip_descifrada
                ?? 'unknown'
            }}
        </div>

        <div>
            os={{
                $intento->sistema_operativo
                ?? 'unknown'
            }}
        </div>

        <div>
            browser={{
                $intento->navegador
                ?? 'unknown'
            }}
        </div>

        <div>
            attempts={{ $intento->intentos }}
        </div>

        <div>
            last_attempt={{
                optional(
                    $intento->ultimo_intento_at
                )->format('d/m/Y H:i:s')
                ?? '-'
            }}
        </div>

        <div>
            blocked_until={{
                optional(
                    $intento->bloqueado_hasta
                )->format('d/m/Y H:i:s')
                ?? '-'
            }}
        </div>

    </article>

    <div class="line"></div>

@empty

    <p class="success">
        [ OK ] No failed access attempts.
    </p>

@endforelse

{{ $intentos->links() }}

@endsection

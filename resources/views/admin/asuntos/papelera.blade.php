@extends('layouts.terminal')

@section('title', 'Admin - Papelera')

@section('terminal-title')
root@anonymous-chat:/admin/trash#
@endsection

@section('prompt')
root@anonymous-chat:/admin/trash#
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
        root@anonymous-chat:/admin/trash#
    </span>

    <span class="command">
        ls --deleted
    </span>
</p>

<div class="line"></div>

<ul class="terminal-list">

    @forelse ($asuntos as $asunto)

        <li>

            <strong>
                {{ $asunto->titulo }}
            </strong>

            <div class="muted">
                deleted={{
                    optional(
                        $asunto->deleted_at
                    )->format('d/m/Y H:i:s')
                }}

                |

                messages={{
                    $asunto
                        ->mensajes_con_eliminados_count
                }}
            </div>

            <form
                method="POST"
                action="{{ route(
                    'admin.papelera.restaurar',
                    $asunto->id
                ) }}"
                style="margin-top:10px;"
            >

                @csrf

                <button type="submit">
                    restore
                </button>

            </form>

        </li>

    @empty

        <li class="muted">
            trash vacío.
        </li>

    @endforelse

</ul>

{{ $asuntos->links() }}

@endsection
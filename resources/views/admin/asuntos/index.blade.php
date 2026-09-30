@extends('layouts.terminal')

@section('title', 'Admin - Asuntos')

@section('terminal-title')
root@anonymous-chat:/admin/asuntos#
@endsection

@section('prompt')
root@anonymous-chat:/admin/asuntos#
@endsection

@section('content')

<div class="nav">

    <a href="{{ route('admin.dashboard') }}">
        [ dashboard ]
    </a>

    <a href="{{ route('admin.papelera') }}">
        [ trash ]
    </a>

    <a href="{{ route('admin.intentos.index') }}">
        [ failed_access ]
    </a>

</div>

<p>
    <span class="prompt">
        root@anonymous-chat:/admin/asuntos#
    </span>

    <span class="command">
        ls -la
    </span>
</p>

<div class="line"></div>

<ul class="terminal-list">

    @forelse ($asuntos as $asunto)

        <li>

            <a
                href="{{ route(
                    'admin.asuntos.show',
                    $asunto
                ) }}"
            >
                {{ $asunto->titulo }}
            </a>

            <div class="muted">

                slug={{ $asunto->slug }}

                |

                messages={{ $asunto->mensajes_count }}

                |

                expires={{
                    optional(
                        $asunto->expira_at
                    )->format('d/m/Y H:i')
                }}

                @if ($asunto->expira_at)

                    |

                    remaining={{
                        $asunto
                            ->expira_at
                            ->diffForHumans()
                    }}

                @endif

            </div>

        </li>

    @empty

        <li class="muted">
            No active threads.
        </li>

    @endforelse

</ul>

<div class="pagination">
    {{ $asuntos->links() }}
</div>

@endsection
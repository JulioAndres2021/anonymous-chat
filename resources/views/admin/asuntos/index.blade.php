@extends('layouts.terminal')

@section('title', 'Admin - Asuntos')

@section('terminal-title')
root@anonymous-chat:/admin/asuntos#
@endsection

@section('prompt')
root@anonymous-chat:/admin/asuntos#
@endsection

@section('content')

<p>
    <span class="prompt">root@anonymous-chat:/admin/asuntos#</span>
    <span class="command">ls -la</span>
</p>

<div class="line"></div>

<ul class="terminal-list">
    @forelse ($asuntos as $asunto)
        <li>
            <a href="{{ route('admin.asuntos.show', $asunto) }}">
                {{ $asunto->titulo }}
            </a>

            <div class="muted">
                slug={{ $asunto->slug }}
                | messages={{ $asunto->mensajes_count }}
                | {{ __('messages.expires') }}={{ optional($asunto->expira_at)->format('d/m/Y H:i') }}

                @if ($asunto->expira_at)
                    | {{ __('messages.remaining') }}={{ $asunto->expira_at->diffForHumans() }}
                @endif
            </div>
        </li>
    @empty
        <li class="muted">{{ __('messages.no_active_threads') }}</li>
    @endforelse
</ul>

<div class="pagination">
    {{ $asuntos->links() }}
</div>

@endsection

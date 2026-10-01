@extends('layouts.terminal')

@section('title', 'Admin - Papelera')

@section('terminal-title')
root@anonymous-chat:/admin/trash#
@endsection

@section('prompt')
root@anonymous-chat:/admin/trash#
@endsection

@section('content')

<p>
    <span class="prompt">root@anonymous-chat:/admin/trash#</span>
    <span class="command">ls --deleted</span>
</p>

<div class="line"></div>

<ul class="terminal-list">
    @forelse ($asuntos as $asunto)
        <li>
            <strong>{{ $asunto->titulo }}</strong>

            <div class="muted">
                {{ __('messages.deleted') }}={{ optional($asunto->deleted_at)->format('d/m/Y H:i:s') }}
                | messages={{ $asunto->mensajes_con_eliminados_count }}
            </div>

            <form
                method="POST"
                action="{{ route('admin.papelera.restaurar', $asunto->id) }}"
                class="stack-form"
            >
                @csrf
                <button type="submit">{{ __('messages.restore') }}</button>
            </form>

            <form
                method="POST"
                action="{{ route(
                    'admin.papelera.destroy',
                    $asunto->id
                ) }}"
                class="stack-form"
                data-confirm="{{
                    __('messages.delete_permanently_confirm')
                }}"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="lock-thread-button"
                >
                    {{ __('messages.delete_permanently') }}
                </button>
            </form>
        </li>
    @empty
        <li class="muted">{{ __('messages.trash_empty') }}</li>
    @endforelse
</ul>

{{ $asuntos->links() }}

@endsection

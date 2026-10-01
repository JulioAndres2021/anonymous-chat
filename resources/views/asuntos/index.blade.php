@extends('layouts.terminal')

@section('title', 'Anonymous Chat')

@section('terminal-title')
anonymous@chat:~$ ls asuntos
@endsection

@section('terminal-image')
<img src="{{ asset('images/anonymous-chat.png') }}" alt="Anonymous Chat">
@endsection

@section('content')

<div class="nav">
    <a href="{{ route('asuntos.create') }}" class="terminal-button">
        + {{ __('messages.create_thread') }}
    </a>
</div>

<div class="message">
    <p class="success">[ {{ __('messages.app_name') }} ]</p>
    <p>{{ __('messages.home_title') }}</p>
    <p class="muted">{{ __('messages.home_search_help') }}</p>
    <p class="muted">{{ __('messages.home_create_help') }}</p>
    <p class="warning">[ {{ __('messages.privacy') }} ] {{ __('messages.privacy_notice') }}</p>
    <p class="muted">{{ __('messages.password_warning') }}</p>
</div>

<div class="line"></div>

<div class="field">
    <label for="buscar-asunto">{{ __('messages.search') }}:</label>
    <input
        id="buscar-asunto"
        type="text"
        placeholder="{{ __('messages.search_placeholder') }}"
        autocomplete="off"
    >
</div>

<p id="search-status" class="muted">{{ __('messages.showing_threads') }}</p>

<div class="line"></div>

<p>
    <span class="prompt">anonymous@chat:~$</span>
    <span class="command">ls asuntos</span>
</p>

<div class="line"></div>

<ul id="lista-asuntos" class="terminal-list">
    @forelse ($asuntos as $asunto)
        <li>
            <a href="{{ route('asuntos.acceso', $asunto) }}">
                {{ $asunto->titulo }}
            </a>

            <span class="muted">
                · {{ __('messages.expires') }}
                {{ optional($asunto->expira_at)->format('d/m/Y H:i') }}
            </span>

            @if ($asunto->expira_at)
                <span class="warning">
                    · {{ __('messages.remaining') }}:
                    {{ $asunto->expira_at->diffForHumans() }}
                </span>
            @endif
        </li>
    @empty
        <li class="muted">{{ __('messages.no_threads') }}</li>
    @endforelse
</ul>

<div
    data-thread-search
    data-search-url="{{ route('asuntos.buscar') }}"
    data-showing-text="{{ __('messages.showing_threads') }}"
    data-no-results-text="{{ __('messages.no_results') }}"
    data-results-text="{{ __('messages.results') }}"
    data-error-text="{{ __('messages.search_error') }}"
    data-expires-text="{{ __('messages.expires') }}"
>
<div
    id="asuntos-pagination"
    class="pagination"
>
    {{ $asuntos->links() }}
</div>

@endsection



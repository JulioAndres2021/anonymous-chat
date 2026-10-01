<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Anonymous Chat')</title>
    @vite([
        'resources/css/app.css',
        'resources/js/app.js',
    ])
   
</head>
<body>

<div class="terminal">

    <div class="terminal-header">
        <div class="terminal-header-content">
            <div class="terminal-header-title">
                @yield('terminal-title', 'anonymous@chat:~$')
            </div>

            @hasSection('terminal-image')
                <div class="terminal-header-image">
                    @yield('terminal-image')
                </div>
            @endif
        </div>
    </div>

    <main class="terminal-body">

        @if (request()->routeIs('admin.*') && auth()->check())
            <div class="nav">
                <a href="{{ route('admin.dashboard') }}">[ {{ __('messages.admin_dashboard') }} ]</a>
                <a href="{{ route('admin.asuntos.index') }}">[ {{ __('messages.admin_threads') }} ]</a>
                <a href="{{ route('admin.papelera') }}">[ {{ __('messages.admin_trash') }} ]</a>
                <a href="{{ route('admin.intentos.index') }}">[ {{ __('messages.admin_security') }} ]</a>

                <form method="POST" action="{{ route('admin.logout') }}" class="inline-form">
                    @csrf
                    <button
                        type="submit"
                        class="logout-button"
                    >
                        [ {{ __('messages.admin_logout') }} ]
                    </button>
                </form>
            </div>

            <div class="line"></div>
        @endif

        <div class="language-switcher">
            <span class="muted">{{ __('messages.language') }}:</span>

            <a
                href="{{ route('locale.change', 'es') }}"
                class="{{ app()->getLocale() === 'es' ? 'success' : '' }}"
            >[ ES ]</a>

            <a
                href="{{ route('locale.change', 'en') }}"
                class="{{ app()->getLocale() === 'en' ? 'success' : '' }}"
            >[ EN ]</a>
        </div>

        @if (session('success'))
            <p class="success">[ {{ __('messages.ok') }} ] {{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div class="error">
                @foreach ($errors->all() as $error)
                    <div>[ {{ __('messages.error') }} ] {{ $error }}</div>
                @endforeach
            </div>

            <div class="line"></div>
        @endif

        @yield('content')
        
        <div class="locale-switcher">

    </main>
</div>

</body>
</html>

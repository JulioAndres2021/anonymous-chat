<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'Anonymous Chat')</title>

    <style>
        * { box-sizing: border-box; }

        html,
        body {
            margin: 0;
            padding: 0;
            min-height: 100%;
            background: #000;
            color: #f5f5f5;
        }

        body {
            font-family: "Courier New", Courier, monospace;
            font-size: 16px;
            line-height: 1.6;
        }

        a { color: #00ff41; text-decoration: none; }
        a:hover { text-decoration: underline; }

        .terminal {
            width: min(1100px, 94%);
            margin: 30px auto;
            border: 1px solid #333;
            background: #050505;
            box-shadow: 0 0 20px rgba(0, 255, 65, 0.06);
        }

        .terminal-header {
            padding: 10px 16px;
            border-bottom: 1px solid #222;
            color: #aaa;
        }

        .terminal-body { padding: 22px; }
        .prompt { color: #00ff41; }
        .command { color: #fff; }
        .muted { color: #888; }
        .success { color: #00ff41; }
        .error { color: #ff5f56; }
        .warning { color: #ffbd2e; }

        .line {
            border-top: 1px dashed #333;
            margin: 18px 0;
        }

        .terminal-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .terminal-list li { padding: 6px 0; }
        .terminal-list li::before { content: "> "; color: #00ff41; }

        input,
        textarea,
        select {
            width: 100%;
            background: #000;
            color: #fff;
            border: 1px solid #444;
            padding: 10px 12px;
            font: inherit;
            outline: none;
        }

        input:focus,
        textarea:focus,
        select:focus { border-color: #00ff41; }

        textarea { resize: vertical; }

        button,
        .terminal-button {
            display: inline-block;
            background: transparent;
            color: #00ff41;
            border: 1px solid #00ff41;
            padding: 9px 14px;
            font: inherit;
            cursor: pointer;
        }

        button:hover,
        .terminal-button:hover {
            background: #00ff41;
            color: #000;
            text-decoration: none;
        }

        .field { margin-bottom: 18px; }
        .field label { display: block; margin-bottom: 6px; color: #aaa; }
        .message { padding: 14px 0; }
        .message-content { white-space: pre-wrap; word-break: break-word; }
        .message-meta { margin-top: 8px; color: #777; font-size: 13px; }
        .pagination { margin-top: 25px; }

        .nav {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
            align-items: center;
        }

        .cursor {
            display: inline-block;
            width: 9px;
            height: 18px;
            background: #00ff41;
            vertical-align: middle;
            animation: blink 1s steps(2, start) infinite;
        }

        @keyframes blink { 50% { opacity: 0; } }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 12px;
            margin: 20px 0;
        }

        .stat { border: 1px solid #333; padding: 14px; }
        .stat-value { font-size: 26px; color: #00ff41; }
        .stat-label { color: #888; }

        .lock-thread-button { color: #ff5f56; border-color: #ff5f56; }
        .lock-thread-button:hover { background: #ff5f56; color: #000; }

        .terminal-header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .terminal-header-title { min-width: 0; }
        .terminal-header-image {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .terminal-header-image img {
            display: block;
            max-width: 150px;
            max-height: 78px;
            object-fit: contain;
            opacity: 0.95;
        }

        .language-switcher {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            margin-bottom: 16px;
        }

        @media (max-width: 700px) {
            .terminal { width: 96%; margin: 12px auto; }
            .terminal-body { padding: 15px; }
            body { font-size: 14px; }
        }

        @media (max-width: 600px) {
            .terminal-header-image img { max-width: 90px; max-height: 50px; }
        }
    </style>

    @stack('styles')
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

                <form method="POST" action="{{ route('admin.logout') }}" style="display:inline;">
                    @csrf
                    <button
                        type="submit"
                        style="padding:0;border:0;background:transparent;color:#ff5f56;"
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

        <div style="margin-top: 24px;">
            <span class="prompt">@yield('prompt', 'anonymous@chat:~$')</span>
            <span class="cursor"></span>
        </div>

    </main>
</div>

@stack('scripts')

</body>
</html>

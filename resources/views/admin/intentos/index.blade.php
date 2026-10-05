@extends('layouts.terminal')

@section('title', 'Admin - Failed Access')

@section('terminal-title')
root@anonymous-chat:/admin/security#
@endsection

@section('prompt')
root@anonymous-chat:/admin/security#
@endsection

@section('content')

<p>
    <span class="prompt">root@anonymous-chat:/admin/security#</span>
    <span class="command">show --failed-access</span>
</p>

<div class="line"></div>

@forelse ($intentos as $intento)
    <article class="message">
        <div>thread={{ $intento->asunto?->titulo ?? __('messages.unknown') }}</div>
        <div>ip={{ $intento->ip_descifrada ?? __('messages.unknown') }}</div>
        <div>{{ __('messages.operating_system') }}={{ $intento->sistema_operativo ?? __('messages.unknown') }}</div>
        <div>{{ __('messages.browser') }}={{ $intento->navegador ?? __('messages.unknown') }}</div>
        <div>{{ __('messages.attempts') }}={{ $intento->intentos }}</div>
        <div>{{ __('messages.last_attempt') }}={{ optional($intento->ultimo_intento_at)->format('d/m/Y H:i:s') ?? '-' }}</div>
        <div>{{ __('messages.blocked_until') }}={{ optional($intento->bloqueado_hasta)->format('d/m/Y H:i:s') ?? '-' }}</div>
    </article>

    <div class="line"></div>
@empty
    <p class="success">[ {{ __('messages.ok') }} ] {{ __('messages.no_failed_access') }}</p>
@endforelse

{{ $intentos->links() }}

@endsection

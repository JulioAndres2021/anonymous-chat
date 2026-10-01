@extends('layouts.terminal')

@section('title', 'Admin Login')

@section('terminal-title')
root@anonymous-chat:/admin# login
@endsection

@section('prompt')
root@anonymous-chat:/admin#
@endsection

@section('content')

<p>
    <span class="prompt">root@anonymous-chat:/admin#</span>
    <span class="command">authenticate</span>
</p>

<div class="line"></div>

<p class="warning">{{ __('messages.restricted_area') }}</p>

<form method="POST" action="{{ route('admin.login.store') }}">
    @csrf

    <div class="field">
        <label for="email">{{ __('messages.user') }}:</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus>
    </div>

    <div class="field">
        <label for="password">{{ __('messages.password') }}:</label>
        <input id="password" type="password" name="password">
    </div>

    <div class="field">
        <label>
            <input type="checkbox" name="remember" value="1" class="checkbox-auto">
            {{ __('messages.remember_session') }}
        </label>
    </div>

    <button type="submit">{{ __('messages.login') }}</button>
</form>

@endsection

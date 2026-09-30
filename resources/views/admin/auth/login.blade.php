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
    <span class="prompt">
        root@anonymous-chat:/admin#
    </span>

    <span class="command">
        authenticate
    </span>
</p>

<div class="line"></div>

<p class="warning">
    RESTRICTED AREA
</p>

<form
    method="POST"
    action="{{ route('admin.login.store') }}"
>
    @csrf

    <div class="field">

        <label for="email">
            user:
        </label>

        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            autofocus
        >

    </div>

    <div class="field">

        <label for="password">
            password:
        </label>

        <input
            id="password"
            type="password"
            name="password"
        >

    </div>

    <div class="field">

        <label>

            <input
                type="checkbox"
                name="remember"
                value="1"
                style="width:auto;"
            >

            remember_session

        </label>

    </div>

    <button type="submit">
        login
    </button>

</form>

@endsection
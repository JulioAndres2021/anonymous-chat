<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Admin - Anonymous Chat
    </title>
</head>

<body>

    <h1>
        Anonymous Chat / Admin
    </h1>

    @if ($errors->any())

        <ul>
            @foreach ($errors->all() as $error)
                <li>
                    {{ $error }}
                </li>
            @endforeach
        </ul>

    @endif

    <form
        method="POST"
        action="{{ route('admin.login.store') }}"
    >

        @csrf

        <div>
            <label for="email">
                Email
            </label>

            <br>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
            >
        </div>

        <br>

        <div>
            <label for="password">
                Password
            </label>

            <br>

            <input
                id="password"
                type="password"
                name="password"
            >
        </div>

        <br>

        <div>
            <label>
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                >

                Recordarme
            </label>
        </div>

        <br>

        <button type="submit">
            Entrar
        </button>

    </form>

</body>
</html>

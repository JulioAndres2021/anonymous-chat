<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Crear asunto</title>
</head>

<body>

    <h1>Crear asunto</h1>

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
        action="{{ route('asuntos.store') }}"
        method="POST"
    >

        @csrf

        <div>
            <label>
                Asunto
            </label>

            <br>

            <input
                type="text"
                name="titulo"
                value="{{ old('titulo') }}"
            >
        </div>

        <br>

        <div>
            <label>
                Contraseña
            </label>

            <br>

            <input
                type="password"
                name="password"
            >
        </div>

        <br>

        <div>
            <label>
                Repetir contraseña
            </label>

            <br>

            <input
                type="password"
                name="password_confirmation"
            >
        </div>

        <br>

        <div>
            <label>
                Fecha de expiración
            </label>

            <br>

            <input
                type="datetime-local"
                name="expira_at"
                value="{{ old('expira_at') }}"
            >
        </div>

        <br>

        <div>
            <label>
                Mensaje
            </label>

            <br>

            <textarea
                name="mensaje"
                rows="8"
                cols="60"
            >{{ old('mensaje') }}</textarea>
        </div>

        <br>

        <button type="submit">
            Crear asunto
        </button>

    </form>

    <br>

    <a href="{{ route('asuntos.index') }}">
        Volver
    </a>

</body>
</html>

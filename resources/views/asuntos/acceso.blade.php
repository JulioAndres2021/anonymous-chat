<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Acceso - {{ $asunto->titulo }}
    </title>
</head>

<body>

    <h1>
        {{ $asunto->titulo }}
    </h1>

    <p>
        Este asunto está protegido.
    </p>

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
        action="{{ route('asuntos.unlock', $asunto) }}"
    >

        @csrf

        <div>

            <label for="password">
                Contraseña
            </label>

            <br>

            <input
                id="password"
                type="password"
                name="password"
                autofocus
            >

        </div>

        <br>

        <button type="submit">
            Desbloquear
        </button>

    </form>

    <br>

    <a href="{{ route('asuntos.index') }}">
        Volver
    </a>

</body>
</html>

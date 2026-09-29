<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard - Anonymous Chat
    </title>
</head>

<body>

    <h1>
        Administración
    </h1>

    <p>
        Bienvenido,
        {{ auth()->user()->name }}
    </p>

    <hr>

    <ul>

        <li>
            Asuntos activos:
            {{ $estadisticas['asuntos_activos'] }}
        </li>

        <li>
            Asuntos eliminados:
            {{ $estadisticas['asuntos_eliminados'] }}
        </li>

        <li>
            Mensajes:
            {{ $estadisticas['mensajes'] }}
        </li>

        <li>
            Adjuntos:
            {{ $estadisticas['adjuntos'] }}
        </li>

        <li>
            Intentos fallidos:
            {{ $estadisticas['intentos_fallidos'] }}
        </li>

    </ul>

    <hr>

    <h2>Administración</h2>

    <ul>
        <li>
            <a href="{{ route('admin.asuntos.index') }}">
                Asuntos activos
            </a>
        </li>

        <li>
            <a href="{{ route('admin.papelera') }}">
                Papelera
            </a>
        </li>

        <li>
            <a href="{{ route('admin.intentos.index') }}">
                Intentos de acceso
            </a>
        </li>
    </ul>

    <hr>

    <form
        method="POST"
        action="{{ route('admin.logout') }}"
    >

        @csrf

        <button type="submit">
            Cerrar sesión
        </button>

    </form>

</body>
</html>

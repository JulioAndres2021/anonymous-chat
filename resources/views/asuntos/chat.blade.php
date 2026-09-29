<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $asunto->titulo }}
    </title>
</head>

<body>

    <h1>
        {{ $asunto->titulo }}
    </h1>

    <p>
        Conversación desbloqueada.
    </p>

    <hr>

    @forelse ($mensajes as $mensaje)

        <div>

            <p>
                {{ $mensaje->contenido_descifrado }}
            </p>

            <small>
                {{ $mensaje->created_at->format('d/m/Y H:i') }}
            </small>

        </div>

        <hr>

    @empty

        <p>
            No hay mensajes.
        </p>

    @endforelse

    <a href="{{ route('asuntos.index') }}">
        Volver al listado
    </a>

</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        {{ $asunto->titulo }} - Admin
    </title>
</head>

<body>

    <h1>
        {{ $asunto->titulo }}
    </h1>

    <p>
        <a href="{{ route('admin.asuntos.index') }}">
            Volver
        </a>
    </p>

    <hr>

    <p>
        <strong>ID:</strong>
        {{ $asunto->id }}
    </p>

    <p>
        <strong>Slug:</strong>
        {{ $asunto->slug }}
    </p>

    <p>
        <strong>Creado:</strong>
        {{ $asunto->created_at->format('d/m/Y H:i') }}
    </p>

    <p>
        <strong>Expira:</strong>
        {{ optional($asunto->expira_at)->format('d/m/Y H:i') }}
    </p>

    <p>
        <strong>Mensajes:</strong>
        {{ $asunto->mensajes_count }}
    </p>

    <p>
        <strong>Intentos registrados:</strong>
        {{ $asunto->intentos_acceso_count }}
    </p>

    <hr>

    <h2>
        Mensajes
    </h2>

    @forelse ($asunto->mensajes as $mensaje)

        <div>

            Mensaje #{{ $mensaje->id }}

            <br>

            Fecha:
            {{ $mensaje->created_at->format('d/m/Y H:i') }}

            <br>

            SO:
            {{ $mensaje->sistema_operativo ?? '-' }}

            <br>

            Navegador:
            {{ $mensaje->navegador ?? '-' }}

            <br>

            Adjuntos:
            {{ $mensaje->adjuntos->count() }}

            <br>

            Contenido:

            <strong>
                CIFRADO
            </strong>

        </div>

        <hr>

    @empty

        <p>
            No hay mensajes.
        </p>

    @endforelse

</body>
</html>

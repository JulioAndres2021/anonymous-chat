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

            <h2>
                Mensajes
            </h2>

            @if ($errorDescifrado)

                <p>
                    {{ $errorDescifrado }}
                </p>

            @else

                @forelse ($mensajesDescifrados as $mensaje)

                    <div>

                        <strong>
                            Mensaje #{{ $mensaje->id }}
                        </strong>

                        <br><br>

                        {{ $mensaje->contenido_descifrado }}

                        <br><br>

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

                        @if ($mensaje->adjuntos->isNotEmpty())

                            @foreach ($mensaje->adjuntos as $adjunto)

                                <p>
                                    <a
                                        href="{{ route(
                                            'admin.adjuntos.show',
                                            $adjunto
                                        ) }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        [ IMAGEN ]
                                    </a>
                                </p>

                            @endforeach

                        @endif

                    </div>

                    <hr>

                @empty

                    <p>
                        No hay mensajes.
                    </p>

                @endforelse

            @endif

        </div>

        <hr>

    @empty

        <p>
            No hay mensajes.
        </p>

    @endforelse

</body>
</html>

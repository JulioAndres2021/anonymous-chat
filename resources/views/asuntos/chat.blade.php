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

            @if ($mensaje->adjuntos->isNotEmpty())

                @foreach ($mensaje->adjuntos as $adjunto)

                    <p>
                        <a
                            href="{{ route(
                                'adjuntos.show',
                                [$asunto, $adjunto]
                            ) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            [ IMAGEN ]
                        </a>
                    </p>

                @endforeach

            @endif

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

    @if (session('success'))
        <p>
            {{ session('success') }}
        </p>
    @endif

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
        action="{{ route('mensajes.store', $asunto) }}"
        enctype="multipart/form-data"
    >
        @csrf

        <div>
            <label for="mensaje">
                Responder
            </label>

            <br>

            <textarea
                id="mensaje"
                name="mensaje"
                rows="6"
                cols="60"
            >{{ old('mensaje') }}</textarea>

            <br>

            <div>
                <label for="imagen">
                    Adjuntar imagen
                </label>

                <br>

                <input
                    id="imagen"
                    type="file"
                    name="imagen"
                    accept="image/jpeg,image/png,image/webp,image/gif"
                >
            </div>
        </div>

        <br>

        <button type="submit">
            Enviar mensaje
        </button>
    </form>

    <a href="{{ route('asuntos.index') }}">
        Volver al listado
    </a>

</body>
</html>

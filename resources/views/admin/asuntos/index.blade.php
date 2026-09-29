<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Asuntos - Admin</title>
</head>

<body>

    <h1>
        Asuntos activos
    </h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">
            Volver al dashboard
        </a>
    </p>

    <hr>

    @forelse ($asuntos as $asunto)

        <div>

            <strong>
                {{ $asunto->titulo }}
            </strong>

            <br>

            Slug:
            {{ $asunto->slug }}

            <br>

            Mensajes:
            {{ $asunto->mensajes_count }}

            <br>

            Expira:
            {{ optional($asunto->expira_at)->format('d/m/Y H:i') }}

            <br><br>

            <a href="{{ route(
                'admin.asuntos.show',
                $asunto
            ) }}">
                Ver detalle
            </a>

        </div>

        <hr>

    @empty

        <p>
            No hay asuntos activos.
        </p>

    @endforelse

    {{ $asuntos->links() }}

</body>
</html>

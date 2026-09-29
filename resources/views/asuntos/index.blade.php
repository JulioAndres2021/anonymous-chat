<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Anonymous Chat</title>
</head>

<body>

    <h1>ANONYMOUS-CHAT</h1>

    <a href="{{ route('asuntos.create') }}">
        Crear asunto
    </a>

    @if (session('success'))
        <p>
            {{ session('success') }}
        </p>
    @endif

    <hr>

    @forelse ($asuntos as $asunto)

        <div>
            <strong>
                <a href="{{ route('asuntos.acceso', $asunto) }}">
                    {{ $asunto->titulo }}
                </a>
            </strong>

            <br>

            Expira:
            {{ optional($asunto->expira_at)->format('d/m/Y H:i') }}
        </div>

        <hr>

    @empty

        <p>No hay asuntos disponibles.</p>

    @endforelse

    {{ $asuntos->links() }}

</body>
</html>

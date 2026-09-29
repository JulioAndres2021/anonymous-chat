<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Papelera - Admin</title>
</head>

<body>

    <h1>
        Papelera
    </h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">
            Volver al dashboard
        </a>
    </p>

    @if (session('success'))

        <p>
            {{ session('success') }}
        </p>

    @endif

    <hr>

    @forelse ($asuntos as $asunto)

        <div>

            <strong>
                {{ $asunto->titulo }}
            </strong>

            <br>

            Eliminado:
            {{ optional($asunto->deleted_at)->format('d/m/Y H:i') }}

            <br>

            Mensajes:
            {{ $asunto->mensajes_con_eliminados_count }}

            <br><br>

            <form
                method="POST"
                action="{{ route(
                    'admin.papelera.restaurar',
                    $asunto->id
                ) }}"
            >

                @csrf

                <button type="submit">
                    Restaurar
                </button>

            </form>

        </div>

        <hr>

    @empty

        <p>
            La papelera está vacía.
        </p>

    @endforelse

    {{ $asuntos->links() }}

</body>
</html>

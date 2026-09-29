<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Intentos de acceso
    </title>
</head>

<body>

    <h1>
        Intentos de acceso
    </h1>

    <p>
        <a href="{{ route('admin.dashboard') }}">
            Volver al dashboard
        </a>
    </p>

    <hr>

    @forelse ($intentos as $intento)

        <div>

            <strong>
                Asunto:
            </strong>

            {{ $intento->asunto?->titulo ?? 'Asunto no disponible' }}

            <br>

            <strong>
                IP:
            </strong>

            {{ $intento->ip_descifrada ?? '-' }}

            <br>

            <strong>
                SO:
            </strong>

            {{ $intento->sistema_operativo ?? '-' }}

            <br>

            <strong>
                Navegador:
            </strong>

            {{ $intento->navegador ?? '-' }}

            <br>

            <strong>
                Intentos:
            </strong>

            {{ $intento->intentos }}

            <br>

            <strong>
                Último intento:
            </strong>

            {{ optional(
                $intento->ultimo_intento_at
            )->format('d/m/Y H:i') }}

            <br>

            <strong>
                Bloqueado hasta:
            </strong>

            {{ optional(
                $intento->bloqueado_hasta
            )->format('d/m/Y H:i') ?? '-' }}

        </div>

        <hr>

    @empty

        <p>
            No hay intentos fallidos registrados.
        </p>

    @endforelse

    {{ $intentos->links() }}

</body>
</html>

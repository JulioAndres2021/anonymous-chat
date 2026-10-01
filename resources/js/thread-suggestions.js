document.addEventListener('DOMContentLoaded', () => {
    const root =
        document.querySelector(
            '[data-thread-suggestions]'
        );

    if (!root) {
        return;
    }

    const input =
        document.getElementById('titulo');

    const contenedor =
        document.getElementById(
            'titulo-sugerencias'
        );

    if (!input || !contenedor) {
        return;
    }

    const suggestionsUrl =
        root.dataset.suggestionsUrl;

    const availableText =
        root.dataset.availableText;

    const existsText =
        root.dataset.existsText;

    const suggestionsText =
        root.dataset.suggestionsText;

    const selectedText =
        root.dataset.selectedText;

    let timeout = null;

    input.addEventListener('input', () => {
        clearTimeout(timeout);

        timeout = setTimeout(
            comprobar,
            400
        );
    });

    async function comprobar() {
        const titulo =
            input.value.trim();

        if (titulo.length < 3) {
            contenedor.replaceChildren();
            return;
        }

        const url =
            `${suggestionsUrl}?titulo=${
                encodeURIComponent(titulo)
            }`;

        try {
            const response =
                await fetch(url, {
                    headers: {
                        Accept:
                            'application/json',
                    },
                });

            if (!response.ok) {
                throw new Error();
            }

            const data =
                await response.json();

            contenedor.replaceChildren();

            if (!data.existe) {
                const disponible =
                    document.createElement(
                        'span'
                    );

                disponible.className =
                    'success';

                disponible.textContent =
                    `[ AVAILABLE ] ${
                        availableText
                    }`;

                contenedor.appendChild(
                    disponible
                );

                return;
            }

            const aviso =
                document.createElement('div');

            aviso.className =
                'warning';

            aviso.textContent =
                `[ EXISTS ] ${existsText}`;

            contenedor.appendChild(aviso);

            const tituloSugerencias =
                document.createElement('div');

            tituloSugerencias.className =
                'muted';

            tituloSugerencias.textContent =
                `${suggestionsText}:`;

            contenedor.appendChild(
                tituloSugerencias
            );

            data.sugerencias.forEach(
                (sugerencia) => {
                    const boton =
                        document.createElement(
                            'button'
                        );

                    boton.type = 'button';

                    boton.className =
                        'suggestion-button';

                    boton.textContent =
                        sugerencia;

                    boton.addEventListener(
                        'click',
                        () => {
                            input.value =
                                sugerencia;

                            contenedor
                                .replaceChildren();

                            const seleccionado =
                                document
                                    .createElement(
                                        'span'
                                    );

                            seleccionado
                                .className =
                                    'success';

                            seleccionado
                                .textContent =
                                    `[ SELECTED ] ${
                                        selectedText
                                    }: ${
                                        sugerencia
                                    }`;

                            contenedor
                                .appendChild(
                                    seleccionado
                                );
                        }
                    );

                    contenedor.appendChild(
                        boton
                    );
                }
            );

        } catch {
            contenedor.replaceChildren();
        }
    }
});
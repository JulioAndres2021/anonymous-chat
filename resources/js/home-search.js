document.addEventListener('DOMContentLoaded', () => {
    const root =
        document.querySelector(
            '[data-thread-search]'
        );

    if (!root) {
        return;
    }

    const input =
        document.getElementById(
            'buscar-asunto'
        );

    const lista =
        document.getElementById(
            'lista-asuntos'
        );

    const estado =
        document.getElementById(
            'search-status'
        );

    const pagination =
        document.getElementById(
            'asuntos-pagination'
        );

    if (!input || !lista || !estado) {
        return;
    }

    const listadoOriginal =
        lista.innerHTML;

    const searchUrl =
        root.dataset.searchUrl;

    const mostrando =
        root.dataset.showingText;

    const sinResultados =
        root.dataset.noResultsText;

    const resultados =
        root.dataset.resultsText;

    const errorBusqueda =
        root.dataset.errorText;

    const expira =
        root.dataset.expiresText;

    let timeout = null;

    input.addEventListener('input', () => {
        clearTimeout(timeout);

        const query =
            input.value.trim();

        if (query === '') {
            lista.innerHTML =
                listadoOriginal;

            estado.textContent =
                mostrando;

            if (pagination) {
                pagination.hidden = false;
            }

            return;
        }

        if (pagination) {
            pagination.hidden = true;
        }

        timeout = setTimeout(
            () => buscar(query),
            250
        );
    });

    async function buscar(query) {
        estado.textContent =
            `search "${query}"...`;

        try {
            const url =
                `${searchUrl}?q=${
                    encodeURIComponent(query)
                }`;

            const response =
                await fetch(url, {
                    headers: {
                        Accept:
                            'application/json',
                    },
                });

            if (!response.ok) {
                throw new Error(
                    'Search failed'
                );
            }

            const asuntos =
                await response.json();

            lista.replaceChildren();

            if (asuntos.length === 0) {
                const li =
                    document.createElement(
                        'li'
                    );

                li.className = 'muted';
                li.textContent =
                    sinResultados;

                lista.appendChild(li);

                estado.textContent =
                    `0 ${resultados}`;

                return;
            }

            asuntos.forEach((asunto) => {
                const li =
                    document.createElement(
                        'li'
                    );

                const enlace =
                    document.createElement(
                        'a'
                    );

                enlace.href =
                    asunto.url;

                enlace.textContent =
                    asunto.titulo;

                li.appendChild(enlace);

                if (asunto.expira) {
                    const meta =
                        document.createElement(
                            'span'
                        );

                    meta.className =
                        'muted';

                    meta.textContent =
                        ` · ${expira} ${
                            asunto.expira
                        }`;

                    li.appendChild(meta);
                }

                lista.appendChild(li);
            });

            estado.textContent =
                `${asuntos.length} ${
                    resultados
                }`;

        } catch {
            estado.textContent =
                errorBusqueda;
        }
    }
});
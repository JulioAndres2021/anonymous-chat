# ANONYMOUS-CHAT

ANONYMOUS-CHAT es una aplicación web desarrollada con Laravel para crear
conversaciones protegidas por contraseña sin necesidad de registrar
usuarios públicos.

La interfaz está inspirada en una terminal Linux y permite crear asuntos
temporales, acceder mediante contraseña, intercambiar mensajes, adjuntar
imágenes cifradas y cerrar manualmente el acceso de una conversación
desde la sesión actual.

La aplicación incluye un panel administrativo para supervisión,
recuperación de asuntos eliminados, revisión de intentos de acceso y
acceso administrativo autorizado al contenido cifrado.

## Características principales

-   Conversaciones públicas sin registro de usuario.
-   Asuntos protegidos mediante contraseña.
-   Contraseñas almacenadas mediante hash; no se almacenan en texto
    plano ni pueden recuperarse.
-   Mensajes cifrados antes de almacenarse.
-   Imágenes adjuntas almacenadas cifradas y fuera del acceso público
    directo.
-   Clave criptográfica independiente por conversación.
-   Protección de la clave de conversación mediante la contraseña del
    asunto.
-   Clave administrativa de recuperación para administración autorizada.
-   Registro de intentos fallidos y bloqueo temporal.
-   Registro técnico de IP, navegador, sistema operativo y User-Agent
    con fines de seguridad.
-   IP almacenada cifrada y HMAC de IP para correlacionar intentos.
-   Expiración automática y Soft Deletes.
-   Restauración de asuntos desde administración.
-   Buscador dinámico y sugerencias de títulos.
-   `LOCK THREAD` para cerrar el acceso de una conversación en la sesión
    actual.
-   Interfaz estilo terminal.
-   Interfaz bilingüe Español / Inglés.
-   Panel administrativo.
-   Rate limiting en operaciones sensibles.
-   Sesiones cifradas y cookies seguras en producción.
-   Content Security Policy (CSP) y headers HTTP de seguridad.
-   Expiración automática mediante scheduler.
-   Papelera administrativa con restauración y eliminación definitiva.
-   Purga programada de archivos pendientes.
-   Suite automatizada de pruebas.

## Modelo de privacidad

ANONYMOUS-CHAT cifra mensajes y archivos almacenados, pero **no
implementa cifrado extremo a extremo (E2EE)**.

Cada conversación utiliza una clave simétrica aleatoria. La clave queda
protegida mediante:

1.  la contraseña definida para el asunto;
2.  una clave administrativa de recuperación.

Por este diseño, un administrador autorizado que posea la clave
administrativa y acceso a la aplicación puede descifrar conversaciones
desde el panel administrativo.

La contraseña del asunto se almacena mediante hash y no puede
recuperarse desde la base de datos.

## Tecnologías

-   PHP 8.4
-   Laravel 13
-   Composer
-   Blade
-   JavaScript
-   Libsodium
-   Base de datos compatible con Laravel
-   PHPUnit / Laravel Test Suite

El proyecto fue desarrollado y probado localmente en Windows con Laragon
y PHP 8.4.

También fue desplegado y verificado en un entorno de producción Alwaysdata
con PHP 8.4 y HTTPS.

## Requisitos

-   PHP 8.4 o compatible con la versión de Laravel utilizada.
-   Composer.
-   Base de datos configurada para Laravel.
-   Extensión Sodium de PHP.
-   Extensiones requeridas por Laravel y el driver de base de datos.
-   Git si se clona desde el repositorio.
-   Servidor web o `php artisan serve` para desarrollo.

Comprobar Sodium:

``` bash
php -m
```

La lista debe incluir `sodium`.

## Instalación

### 1. Clonar

``` bash
git clone <URL-DEL-REPOSITORIO>
cd anonymous-chat
```

El repositorio es privado y requiere autorización.

### 2. Dependencias

``` bash
composer install
```

### 3. Crear `.env`

Windows:

``` powershell
Copy-Item .env.example .env
```

Linux/macOS:

``` bash
cp .env.example .env
```

### 4. Generar APP_KEY

``` bash
php artisan key:generate
```

### 5. Configurar base de datos

Editar `.env` según el entorno. Ejemplo conceptual:

``` env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=anonymous_chat
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Generar IP_HASH_KEY

``` bash
php -r "echo base64_encode(random_bytes(32)), PHP_EOL;"
```

Guardar el resultado:

``` env
IP_HASH_KEY=VALOR_GENERADO
```

### 7. Generar ANONYMOUS_ADMIN_KEY

Generar una clave diferente:

``` bash
php -r "echo base64_encode(random_bytes(32)), PHP_EOL;"
```

Guardar:

``` env
ANONYMOUS_ADMIN_KEY=VALOR_GENERADO
```

**No utilizar la misma clave para `IP_HASH_KEY` y
`ANONYMOUS_ADMIN_KEY`.**

### 8. Migraciones

``` bash
php artisan migrate
```

### 9. Limpiar cachés

``` bash
php artisan optimize:clear
```

### 10. Ejecutar

``` bash
php artisan serve
```

Por defecto: `http://127.0.0.1:8000`

## Variables sensibles

Deben permanecer en `.env` o en un sistema seguro de secretos:

``` env
APP_KEY=
IP_HASH_KEY=
ANONYMOUS_ADMIN_KEY=
```

Nunca deben subirse sus valores reales al repositorio.

-   `APP_KEY`: criptografía interna de Laravel.
-   `IP_HASH_KEY`: HMAC utilizado para identificar intentos de una misma
    IP.
-   `ANONYMOUS_ADMIN_KEY`: recuperación administrativa de claves de
    conversación.

`ANONYMOUS_ADMIN_KEY` es especialmente sensible. Si se pierde puede
impedir recuperación administrativa; si se compromete junto con los
datos necesarios puede comprometer conversaciones administrativamente
recuperables.

## Flujo de conversación

### Crear asunto

El usuario proporciona título, contraseña, confirmación, expiración y
mensaje inicial.

Al crear:

1.  se genera una clave aleatoria de conversación;
2.  se cifra el mensaje;
3.  la contraseña se almacena mediante hash;
4.  la clave se protege con la contraseña;
5.  se genera la protección administrativa;
6.  se almacena el asunto.

### Acceder

Con contraseña correcta se recupera temporalmente la clave de
conversación en la sesión y se permite leer, responder y abrir adjuntos
autorizados.

### LOCK THREAD

`[ LOCK THREAD ]` elimina de la sesión la clave temporal del asunto. No
elimina mensajes ni la conversación. Para volver a entrar se requiere
nuevamente la contraseña.

## Cifrado

Los mensajes no se almacenan como texto plano. El servicio utiliza
Libsodium `secretbox`, una clave aleatoria por conversación, nonce
aleatorio y ciphertext autenticado.

## Adjuntos

Se admiten imágenes JPG/JPEG, PNG, WEBP y GIF.

Los archivos se cifran con la clave de conversación, se almacenan de
forma privada y no se exponen directamente desde `public/`. En el chat
se muestran mediante un enlace `[ IMAGEN ]` y se descifran únicamente a
través de rutas autorizadas.

## Seguridad de acceso

El sistema registra por asunto:

-   IP cifrada;
-   HMAC de IP;
-   sistema operativo;
-   navegador;
-   User-Agent;
-   cantidad de intentos;
-   último intento;
-   bloqueo temporal.

Al alcanzar el límite de fallos se aplica un bloqueo temporal.

La aplicación **no realiza geolocalización por país, provincia o
ciudad**.

## Expiración automática

El comando:

``` bash
php artisan anonymous:expirar-asuntos
```

aplica eliminación lógica a asuntos vencidos, mensajes y adjuntos. Los
archivos cifrados no se destruyen físicamente en esta operación para
permitir recuperación administrativa mientras sigan disponibles.

## Scheduler

El proyecto utiliza el scheduler de Laravel para automatizar tareas de
mantenimiento.

Programación actual:

``` text
Cada minuto  -> anonymous:expirar-asuntos
Cada hora    -> anonymous:purgar-archivos
```

Desarrollo:

``` bash
php artisan schedule:work
```

Para comprobar las tareas registradas:

``` bash
php artisan schedule:list
```

Para ejecutar manualmente las tareas que correspondan al momento actual:

``` bash
php artisan schedule:run
```

### Producción en Alwaysdata

Alwaysdata ejecuta una única tarea programada cada minuto:

``` bash
cd /home/julioandres/anonymous-chat && php artisan schedule:run
```

Laravel decide internamente qué comandos deben ejecutarse según la
programación definida en `routes/console.php`.

Se verificó en producción el ciclo completo de expiración: un asunto
vencido desaparece del listado público, pasa automáticamente a la papelera
administrativa y posteriormente puede restaurarse o eliminarse
definitivamente.

## Administración

Los usuarios públicos no necesitan registrarse ni iniciar sesión. La
administración utiliza la tabla `users` de Laravel.

El panel permite consultar estadísticas, asuntos, mensajes mediante
recuperación administrativa, adjuntos, intentos fallidos y papelera,
además de restaurar asuntos, eliminar definitivamente elementos de la
papelera y cerrar sesión.

La eliminación definitiva registra y procesa la purga de archivos
cifrados asociados. El comando `anonymous:purgar-archivos` permite
reintentar las purgas pendientes de forma programada.

Al cerrar sesión administrativa se vuelve a la página principal pública.

### Crear administrador de desarrollo

``` bash
php artisan tinker
```

Crear un usuario con `App\Models\User` según los campos definidos por el
proyecto.

No documentar ni reutilizar credenciales reales de desarrollo en
producción.

## Idiomas

Idiomas disponibles:

-   Español (`es`)
-   Inglés (`en`)

El selector `[ ES ] [ EN ]` guarda la preferencia en sesión.

Archivos:

``` text
lang/es/messages.php
lang/en/messages.php
```

## Buscador y sugerencias

La home dispone de búsqueda dinámica. Al vaciar el campo se restaura el
listado original.

Al crear un asunto se comprueba si el título existe y se ofrecen
alternativas. La comprobación incluye asuntos eliminados lógicamente.

Las consultas dinámicas se realizan mediante `fetch` y se identifican
como peticiones AJAX con `X-Requested-With: XMLHttpRequest`.

Si la validación del formulario de creación falla, la aplicación vuelve
explícitamente a `/crear`, evitando que una petición AJAX de sugerencias
pueda convertirse accidentalmente en el destino del redirect de
validación.

## Rutas públicas principales

  Método   Ruta                               Función
  -------- ---------------------------------- ----------------------
  GET      `/`                                Listar asuntos
  GET      `/crear`                           Crear asunto
  POST     `/asuntos`                         Guardar asunto
  GET      `/buscar-asuntos`                  Búsqueda dinámica
  GET      `/asuntos/sugerencias`             Sugerencias
  GET      `/a/{asunto}`                      Solicitar contraseña
  POST     `/a/{asunto}/unlock`               Desbloquear
  GET      `/a/{asunto}/chat`                 Conversación
  POST     `/a/{asunto}/mensajes`             Responder
  POST     `/a/{asunto}/lock`                 LOCK THREAD
  GET      `/a/{asunto}/adjuntos/{adjunto}`   Adjunto autorizado
  GET      `/idioma/{locale}`                 Cambiar idioma

## Administración: rutas principales

Bajo `/admin`:

``` text
/admin/login
/admin
/admin/asuntos
/admin/papelera
/admin/intentos
/admin/adjuntos/{adjunto}
```

Salvo el login, las operaciones administrativas requieren autenticación.

## Estructura relevante

``` text
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   ├── AccesoAsuntoController.php
│   │   ├── AdjuntoController.php
│   │   ├── AsuntoController.php
│   │   └── MensajeController.php
│   └── Middleware/
├── Models/
└── Services/

lang/
├── en/messages.php
└── es/messages.php

resources/views/
├── admin/
├── asuntos/
└── layouts/

routes/
├── console.php
└── web.php

tests/
├── Feature/
└── Unit/
```

## Tests

``` bash
php artisan test
```

Estado verificado:

``` text
24 tests passed
62 assertions
```

La suite cubre cifrado, recuperación administrativa, acceso por
contraseña, bloqueo por intentos, LOCK THREAD, adjuntos, restauración,
autenticación admin, búsqueda, sugerencias, expiración, idiomas y
respuestas protegidas.

## Comandos útiles

``` bash
php artisan optimize:clear
php artisan optimize
php artisan route:list
php artisan test
php artisan anonymous:expirar-asuntos
php artisan anonymous:purgar-archivos
php artisan schedule:list
php artisan schedule:run
php artisan schedule:work
```

## Laragon

Ubicación local utilizada durante el desarrollo:

``` text
C:\laragon\www\anonymous-chat
```

Configurar `.env`, instalar dependencias y migrar antes de ejecutar.

## Producción y hardening

El proyecto fue desplegado y verificado en Alwaysdata utilizando PHP 8.4,
Composer, MySQL/MariaDB, HTTPS y un Document Root apuntando al directorio
`public/` de Laravel.

Configuración esencial de producción:

``` env
APP_ENV=production
APP_DEBUG=false

SESSION_DRIVER=database
SESSION_LIFETIME=60
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax

IP_HASH_KEY=
ANONYMOUS_ADMIN_KEY=
```

Los valores reales de las claves nunca deben incluirse en el repositorio
ni en documentación pública.

Después de modificar variables de entorno o configuración:

``` bash
php artisan optimize:clear
php artisan optimize
```

### Headers de seguridad

En producción se verificaron:

-   `Content-Security-Policy`;
-   `Strict-Transport-Security`;
-   `X-Content-Type-Options: nosniff`;
-   `X-Frame-Options: DENY`;
-   `Referrer-Policy: no-referrer`;
-   `Permissions-Policy` restringiendo cámara, micrófono y geolocalización.

La CSP utiliza recursos propios (`'self'`) y no depende de
`unsafe-inline` para scripts o estilos.

### Sesiones y cookies

Las sesiones se almacenan en base de datos y su contenido persistido se
cifra mediante Laravel.

En producción las cookies se verificaron con `Secure`, `HttpOnly` y
`SameSite=Lax`.

### Rate limiting

Las operaciones sensibles utilizan limitadores independientes para
búsqueda, sugerencias, creación, desbloqueo, mensajes, login administrativo
y acceso a adjuntos.

### Validación de adjuntos

Las imágenes permitidas son JPG/JPEG, PNG, WEBP y GIF, con un límite de
5 MB. Además de la validación de Laravel, el servicio comprueba MIME,
tamaño y que el archivo pueda interpretarse realmente como una imagen.

Los adjuntos cifrados permanecen en almacenamiento privado y no se
publican mediante `storage:link`.

### Claves criptográficas de producción

`IP_HASH_KEY` y `ANONYMOUS_ADMIN_KEY` deben generarse por separado:

``` bash
php -r "echo base64_encode(random_bytes(32)), PHP_EOL;"
```

Nunca reutilizar una clave para ambas funciones. `ANONYMOUS_ADMIN_KEY`
debe conservarse también en un lugar seguro fuera del servidor.

## Despliegue en Alwaysdata

El despliegue probado utiliza PHP 8.4, Laravel 13, Composer, Node.js/npm
para Vite, MySQL/MariaDB, HTTPS y el scheduler de Alwaysdata.

El sitio debe apuntar a:

``` text
/home/<usuario>/anonymous-chat/public
```

Instalación inicial de producción:

``` bash
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan migrate --force
php artisan optimize
```

Una vez compilados los assets, `node_modules` puede eliminarse:

``` bash
rm -rf node_modules
```

### Actualizar producción desde GitHub

``` bash
cd ~/anonymous-chat
git pull
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
rm -rf node_modules
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```

Antes de cambios importantes se recomienda disponer de un backup
verificable. No sobrescribir el `.env` de producción con el de desarrollo.

## Consideraciones de seguridad

Nunca incluir en commits, logs, capturas o documentación pública:

-   contraseñas;
-   claves de conversación;
-   `APP_KEY`;
-   `IP_HASH_KEY`;
-   `ANONYMOUS_ADMIN_KEY`;
-   `.env`;
-   credenciales administrativas;
-   contenido privado.

## Estado actual

``` text
Creación anónima
        ↓
Acceso protegido por contraseña
        ↓
Conversación cifrada
        ↓
Adjuntos cifrados
        ↓
LOCK THREAD
        ↓
Expiración automática / Soft Delete
        ↓
Papelera administrativa
        ↓
Restauración o eliminación definitiva
        ↓
Purga programada de archivos
        ↓
Administración y recuperación autorizada
```

El proyecto se encuentra desplegado en producción sobre Alwaysdata.

Se verificaron en producción la creación y acceso a asuntos, HTTPS, CSP y
headers de seguridad, cookies seguras, sesiones cifradas, claves
criptográficas requeridas, scheduler, expiración automática, papelera,
restauración, eliminación definitiva, búsqueda y sugerencias AJAX y
redirects correctos ante errores de validación.

## Próxima etapa

-   verificar de punta a punta imágenes cifradas en producción;
-   confirmar los reintentos periódicos de `anonymous:purgar-archivos`;
-   verificar físicamente la eliminación de archivos `.enc`;
-   definir y probar una estrategia de backup y restauración;
-   revisar periódicamente logs y espacio de almacenamiento;
-   mantener probado el procedimiento de actualización de producción.

------------------------------------------------------------------------

**ANONYMOUS-CHAT**\
Conversaciones temporales protegidas por contraseña con almacenamiento
cifrado e interfaz estilo terminal.

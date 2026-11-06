# Monitor de Servidor en Tiempo Real — TP 2

Aplicación Laravel que muestra las métricas del servidor por WebSocket usando **Laravel Reverb + Laravel Echo**, con gráficos en vivo de **Chart.js**, apertura de programas en el servidor mediante una lista blanca y una lista de actividad compartida entre todos los navegadores.

**Repositorio:** https://github.com/nadiamc/monitor-tiemporeal

## Stack

- Laravel 13
- Laravel Reverb (WebSockets)
- Laravel Echo + Pusher JS
- Chart.js
- PowerShell (lectura de métricas en Windows)

## Requisitos

- PHP 8.3 o superior
- Composer
- Node.js y npm
- Windows 10 u 11 (para las métricas de CPU/memoria y para abrir Calculadora/Bloc)

## Instalación

```powershell
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

Si el `.env` no tiene las credenciales de Reverb, ejecutar:

```powershell
php artisan install:broadcasting --reverb
```

Verificar que el `.env` tenga:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=...
REVERB_APP_KEY=...
REVERB_APP_SECRET=...
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

DEMO_APPS=true
```

> `DEMO_APPS=true` habilita los botones de la demo. Si se cambia el `.env`, ejecutar `php artisan config:clear`.

## Ejecución

Se necesitan cuatro terminales en la carpeta del proyecto:

| Terminal | Comando | Función |
|---|---|---|
| 1 | `php artisan reverb:start --debug` | Servidor WebSocket en el puerto 8080 |
| 2 | `php artisan schedule:work` | Ejecuta `metrics:broadcast` cada 5 segundos |
| 3 | `npm run dev` | Compila el JavaScript con Vite |
| 4 | `php artisan serve` | Servidor web en `http://127.0.0.1:8000` |

Si no se quiere usar `npm run dev`, alcanza con `npm run build` una vez y luego `php artisan serve`.

Abrir:

- `http://127.0.0.1:8000/` — página de inicio
- `http://127.0.0.1:8000/servidor` — monitor en vivo
- `http://127.0.0.1:8000/graficos` — gráficos en tiempo real

## Rutas

| Método | Ruta | Acción |
|---|---|---|
| GET | `/` | Página de inicio |
| GET | `/servidor` | Monitor en vivo (`ServerController@index`) |
| GET | `/graficos` | Vista de gráficos |
| POST | `/demo/abrir/{app}` | Abre un programa de la lista blanca (`DemoController@abrir`) |

## Cómo funciona

1. **Scheduler**: `routes/console.php` programa `metrics:broadcast` con `everyFiveSeconds()`.
2. **Comando**: `BroadcastMetrics` obtiene un snapshot de `ServerMetrics` y dispara `MetricsUpdated`.
3. **Evento**: `MetricsUpdated` implementa `ShouldBroadcastNow` y publica en el canal público `server.metrics`.
4. **Monitor**: `resources/views/servidor.blade.php` escucha el canal con Echo y actualiza las tarjetas sin recargar.
5. **Botones**: el navegador envía una clave (`calculadora` o `bloc`); `DemoController` la traduce con la constante `APPS`, abre el programa con `popen` y dispara `AppOpened` por el canal `server.demo`.
6. **Gráficos**: `resources/views/graficos.blade.php` escucha el mismo canal `server.metrics` y dibuja CPU y memoria como líneas, y el disco como medidor.

## Respuestas a las preguntas

### 1. ¿Qué diferencia hay en la pestaña Red entre el TP 1 (polling) y este TP?

En el TP 1 el navegador hacía un pedido HTTP nuevo cada 5 segundos para preguntar si había datos. En el TP 2 se abre **una sola conexión WebSocket** que queda abierta; el servidor envía los mensajes cuando corresponde y no hay pedidos repetidos por cada navegador. En la pestaña Red, con el filtro **WS**, se ve una conexión y un mensaje nuevo por cada lectura.

### 2. ¿Qué pasa con la vista si se detiene `reverb:start`? ¿Y si se detiene `schedule:work`?

- Si se detiene **`reverb:start`**, la conexión WebSocket se corta y el indicador pasa a **“Sin conexión”**. La página sigue cargando, pero deja de recibir métricas y actividad.
- Si se detiene **`schedule:work`**, el WebSocket sigue conectado y el indicador dice **“Conectado”**, pero los valores quedan congelados porque no se ejecuta más `metrics:broadcast`. Los botones pueden seguir funcionando si Reverb está levantado, porque `DemoController` dispara `AppOpened` directamente.

### 3. ¿Por qué PowerShell se ejecuta la misma cantidad de veces con 1 o con 30 personas mirando?

Porque PowerShell se ejecuta **una vez por tick del scheduler**, no una vez por navegador. El comando `metrics:broadcast` lee las métricas una sola vez y Reverb distribuye el mismo mensaje a todos los clientes conectados. El costo no depende de la cantidad de espectadores.

### 4. ¿Por qué `DemoController` usa una lista blanca en lugar de recibir el nombre del programa desde el navegador?

Porque recibir un comando desde el navegador permitiría **ejecución remota de comandos**: cualquiera en la red podría ejecutar cualquier programa del servidor. El navegador solo envía una clave (`calculadora` o `bloc`); el servidor la traduce con la constante `APPS`. Una clave desconocida devuelve 404, tanto en la ruta (`whereIn`) como en el controlador.

### 5. ¿Qué diferencia hay entre `ShouldBroadcast` y `ShouldBroadcastNow`?

`ShouldBroadcast` manda el evento a la cola, por lo que necesita un proceso `queue:work` para que el mensaje salga. `ShouldBroadcastNow` lo envía en el momento, sin cola. En este TP se usa `ShouldBroadcastNow` para no necesitar un quinto proceso.

### 6. ¿Por qué la vista de gráficos no necesitó cambios en el servidor? ¿Qué ventaja tiene usar un componente?

Porque la vista de gráficos escucha el **mismo canal** `server.metrics` que ya emite `MetricsUpdated`; Reverb entrega el mensaje a todas las páginas suscriptas. El componente Blade `grafico-metrica` evita repetir el HTML de la tarjeta tres veces: se usa con distintos `titulo`, `campo`, `color` y `tipo`, y el script crea el gráfico a partir de los atributos `data-`.

## Seguridad

- La lista blanca de programas evita inyección de comandos.
- La demo se puede desactivar con `DEMO_APPS=false`.
- La ruta de demo tiene `throttle:2,1` para limitar a dos aperturas por minuto por cliente.
- El canal `server.metrics` y `server.demo` son canales públicos; no se exponen datos privados.

## Capturas para la entrega

1. Página de inicio (`/`).
2. `/servidor` con el indicador en **“Conectado”** y al menos una línea de actividad.
3. `/graficos` con al menos un minuto de datos en las líneas.
4. Pestaña **Red → WS** mostrando los mensajes que llegan.
5. Repositorio en GitHub con el proyecto y este README.

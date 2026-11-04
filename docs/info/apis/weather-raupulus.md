# Integración Meteorológica y Mareas — API `api.raupulus.dev`

*Última actualización: 2026-11-04*

## 1. Propósito de la Integración

Para desacoplar el portal **Desde Chipiona** (`desdechipiona.es`) de proveedores meteorológicos de terceros y evitar almacenar tokens como la API key de AEMET OpenData en este repositorio de código, la aplicación delega la obtención del tiempo y las mareas en la API centralizada del autor:

- **Endpoint de producción:** `https://api.raupulus.dev/v1/chipiona/weather-tides`
- **Variable de entorno:** `RAUPULUS_WEATHER_API_URL`
- **Modo de consumo:** Consumidor pasivo en backend a través de `App\Services\ChipionaWeatherService`.

---

## 2. Dominios Permitidos (CORS / Whitelist)

Para que `api.raupulus.dev` acepte las peticiones procedentes de Desde Chipiona, se deben configurar los siguientes orígenes permitidos en la configuración de la API:

```
https://desdechipiona.es
https://www.desdechipiona.es
http://localhost:8000
http://127.0.0.1:8000
```

Cabeceras HTTP que envía la aplicación en cada petición:
- `Origin: https://desdechipiona.es`
- `Referer: https://desdechipiona.es`
- `Accept: application/json`
- `User-Agent: DesdeChipiona/1.0 (+https://desdechipiona.es)`

---

## 3. Contrato JSON de Respuesta

El endpoint debe responder con código HTTP `200 OK`, `Content-Type: application/json` y la siguiente estructura de datos:

```json
{
  "municipality": "Chipiona (Cádiz)",
  "aemet_code": "11016",
  "updated_at": "04/11/2026 13:00",
  "weather": {
    "source": "AEMET OpenData Oficial (11016)",
    "temp_max": 23,
    "temp_min": 16,
    "sky_state": "Despejado con brisa atlántica de Poniente",
    "wind_direction": "Poniente (O)",
    "wind_speed_kmh": 18,
    "uv_max": 6,
    "rain_prob": 0,
    "sea_temp": 19
  },
  "tides": {
    "coefficient": 88,
    "is_aguaje": true,
    "type_label": "Marea Viva / Aguaje Grande (Ideal para ver los corrales en bajamar)",
    "lunar_day": 14,
    "events": [
      {
        "type": "pleamar",
        "label": "1.ª Pleamar (Marea llena)",
        "time": "03:45",
        "height_m": 3.42,
        "corrales_note": "Corrales cubiertos por el Atlántico."
      },
      {
        "type": "bajamar",
        "label": "1.ª Bajamar (Marea vacía)",
        "time": "09:58",
        "height_m": 0.52,
        "corrales_note": "Momento óptimo para observar la piedra ostionera sin pisar el escaramujo."
      },
      {
        "type": "pleamar",
        "label": "2.ª Pleamar (Marea llena)",
        "time": "16:10",
        "height_m": 3.35,
        "corrales_note": "Entrada de pescado y marisco a los corrales."
      },
      {
        "type": "bajamar",
        "label": "2.ª Bajamar (Marea vacía)",
        "time": "22:25",
        "height_m": 0.61,
        "corrales_note": "Descubierta la restinga de Salmedina y muros de pesca."
      }
    ]
  }
}
```

---

## 4. Política de Caché y Resiliencia en Frontend

- **Caché en servidor:** `ChipionaWeatherService` almacena en caché la respuesta durante **30 minutos** (`1800` segundos) mediante `Cache::remember()`, minimizando la latencia para los visitantes y evitando peticiones redundantes.
- **Fallback local:** Si la API externa no está disponible, no responde en el tiempo límite (timeout de 4 segundos) o devuelve un código de error, el servicio aplica automáticamente un cálculo astronómico local para las mareas y devuelve datos climatológicos seguros sin romper jamás la experiencia de usuario ni generar errores 500.

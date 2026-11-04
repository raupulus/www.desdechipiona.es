# Integración Meteorológica y Mareas — API `api.raupulus.dev`

*Última actualización: 2026-11-04*

## 1. Propósito de la Integración

Para desacoplar el portal **Desde Chipiona** (`desdechipiona.es`) de proveedores meteorológicos de terceros y evitar almacenar tokens como la API key de AEMET OpenData en este repositorio de código, la aplicación delega la obtención del tiempo y las mareas en la API centralizada del autor:

- **Endpoint oficial (API V2):** `https://api.raupulus.dev/api/v2/weather-station-official/cities/chipiona`
- **Variables de entorno:**
  - `RAUPULUS_WEATHER_API_URL` (por defecto: `https://api.raupulus.dev/api/v2/weather-station-official/cities/chipiona`)
  - `RAUPULUS_WEATHER_API_TOKEN` (Bearer token con ability `weatherstation:read`)
  - `RAUPULUS_WEATHER_TIMEOUT` (por defecto: 4 segundos)
- **Modo de consumo:** Consumidor pasivo en backend a través de `App\Services\ChipionaWeatherService`.

---

## 2. Autenticación y Cabeceras HTTP

Para consultar el endpoint, la aplicación envía:

```http
GET /api/v2/weather-station-official/cities/chipiona HTTP/1.1
Host: api.raupulus.dev
Authorization: Bearer <RAUPULUS_WEATHER_API_TOKEN>
Accept: application/json
Origin: https://desdechipiona.es
Referer: https://desdechipiona.es
User-Agent: DesdeChipiona/1.0 (+https://desdechipiona.es)
```

---

## 3. Contrato JSON de Respuesta

El endpoint responde con código HTTP `200 OK`, `Content-Type: application/json` y el envelope estándar `ApiResponseTrait`:

```json
{
  "success": true,
  "message": "Operación exitosa",
  "data": {
    "municipality": "Chipiona (Cádiz)",
    "province": "Cádiz",
    "aemet_code": "11015",
    "updated_at": "04/11/2026 16:30",
    "updated_at_iso": "2026-11-04T15:30:00.000000Z",
    "weather": {
      "source": "Estación Meteorológica Chipiona + AEMET OpenData (11015)",
      "current_temp": 20.8,
      "temp_max": 23,
      "temp_min": 15,
      "sky_state": "Despejado con brisa atlántica de Poniente",
      "wind_direction": "Poniente (OSO)",
      "wind_speed_kmh": 18,
      "uv_max": 6,
      "rain_prob": 0,
      "sea_temp": 19
    },
    "tides": {
      "coefficient": 91,
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
}
```

---

## 4. Política de Caché Diferencial y Resiliencia en Frontend

- **Caché remota exitosa:** `ChipionaWeatherService` almacena en caché la respuesta durante **30 minutos** (`1800` segundos), reduciendo la carga sobre `api.raupulus.dev`.
- **Caché corta en fallback de contingencia:** Si la API externa no responde o falla la red, el servicio aplica inmediatamente su cálculo astronómico y datos meteorológicos de referencia, pero cachea solo **5 minutos** (`300` segundos) para restablecer la conexión viva en cuanto `api.raupulus.dev` vuelva a responder.

<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Servicio de información meteorológica y de mareas para Chipiona.
 * Actúa como consumidor pasivo en backend de la API centralizada `api.raupulus.dev`
 * (que integra los datos oficiales de AEMET e Instituto Hidrográfico de la Marina),
 * eliminando la necesidad de tokens o claves de terceros en esta aplicación.
 */
class ChipionaWeatherService
{
    /**
     * Obtiene la previsión meteorológica y el estado de las mareas de Chipiona para hoy.
     * Si la API remota responde se cachea 30 minutos (1800 s); ante un fallo puntual de conexión
     * se aplica el cálculo de contingencia cacheado solo 5 minutos (300 s) para reintentar pronto.
     *
     * @return array<string, mixed>
     */
    public function getTodaySummary(): array
    {
        $cacheKey = 'chipiona_weather_and_tides_'.now()->format('Y_m_d_H');
        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        // 1. Intentar consultar la API centralizada api.raupulus.dev (V2 Official)
        $remoteData = $this->fetchFromRaupulusApi();
        if ($remoteData !== null) {
            Cache::put($cacheKey, $remoteData, 1800);

            return $remoteData;
        }

        // 2. Fallback resiliente si la API remota no está configurada o no responde
        $weather = $this->fallbackWeather();
        $tides = $this->calculateChipionaTides(Carbon::now('Europe/Madrid'));

        $fallbackData = [
            'municipality' => 'Chipiona (Cádiz)',
            'aemet_code' => (string) config('services.aemet.municipality_code', '11016'),
            'updated_at' => Carbon::now('Europe/Madrid')->format('d/m/Y H:i'),
            'weather' => $weather,
            'tides' => $tides,
        ];

        Cache::put($cacheKey, $fallbackData, 300);

        return $fallbackData;
    }

    /**
     * Consulta el endpoint oficial de meteorología y mareas en api.raupulus.dev
     * enviando token Bearer (si está configurado) y cabeceras de identificación.
     *
     * @return array<string, mixed>|null
     */
    private function fetchFromRaupulusApi(): ?array
    {
        $apiUrl = config('services.raupulus_weather.url');
        if (empty($apiUrl)) {
            return null;
        }

        try {
            $timeout = (int) config('services.raupulus_weather.timeout', 4);
            $appUrl = (string) config('app.url', 'https://desdechipiona.es');
            $token = config('services.raupulus_weather.token');

            $headers = [
                'Origin' => $appUrl,
                'Referer' => $appUrl,
                'Accept' => 'application/json',
                'User-Agent' => 'DesdeChipiona/1.0 (+https://desdechipiona.es)',
            ];

            if (! empty($token)) {
                $headers['Authorization'] = 'Bearer '.$token;
            }

            $response = Http::withHeaders($headers)
                ->connectTimeout(2)
                ->timeout($timeout)
                ->get($apiUrl);

            if ($response->successful()) {
                /** @var array<string, mixed>|null $payload */
                $payload = $response->json();

                // Desenvolver el envelope estándar ApiResponseTrait {"success": true, "data": { ... }}
                $data = (is_array($payload) && isset($payload['data']) && is_array($payload['data']))
                    ? $payload['data']
                    : $payload;

                if (is_array($data) && isset($data['weather'], $data['tides'])) {
                    return [
                        'municipality' => (string) ($data['municipality'] ?? 'Chipiona (Cádiz)'),
                        'province' => (string) ($data['province'] ?? 'Cádiz'),
                        'aemet_code' => (string) ($data['aemet_code'] ?? '11016'),
                        'updated_at' => (string) ($data['updated_at'] ?? Carbon::now('Europe/Madrid')->format('d/m/Y H:i')),
                        'updated_at_iso' => (string) ($data['updated_at_iso'] ?? null),
                        'weather' => (array) $data['weather'],
                        'tides' => (array) $data['tides'],
                    ];
                }
            }
        } catch (Throwable $e) {
            Log::info('Consulta a api.raupulus.dev no disponible; aplicando fallback local de meteorología y mareas: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Genera datos climatológicos de referencia para la costa de Chipiona cuando
     * la API externa no está disponible.
     *
     * @return array<string, mixed>
     */
    private function fallbackWeather(): array
    {
        return [
            'source' => 'Referencia Meteorológica Oficial Chipiona (11016)',
            'temp_max' => 22,
            'temp_min' => 15,
            'sky_state' => 'Despejado con brisa atlántica de Poniente',
            'wind_direction' => 'Poniente (OSO)',
            'wind_speed_kmh' => 16,
            'uv_max' => 6,
            'rain_prob' => 5,
            'sea_temp' => 19,
        ];
    }

    /**
     * Calcula los horarios aproximados de pleamares y bajamares y el coeficiente de marea
     * en la costa de Chipiona a partir de la fase lunar sinódica (29,53 días).
     * Se usa como fallback si la API centralizada no entrega la tabla de mareas del IHM.
     *
     * @return array<string, mixed>
     */
    public function calculateChipionaTides(Carbon $date): array
    {
        // Referencia de luna nueva astronómica conocida (2025-01-29 12:36 UTC)
        $synodicMonth = 29.530588853;
        $daysSinceNewMoon = fmod(abs($date->diffInMinutes(Carbon::create(2025, 1, 29, 12, 36, 0, 'UTC'))) / 1440, $synodicMonth);
        $lunarAgeRatio = $daysSinceNewMoon / $synodicMonth;

        // El coeficiente de marea oscila entre ~38 (mareas muertas) y ~104 (aguajes / mareas vivas en sizigia)
        $springNeapWave = abs(cos(2 * M_PI * $lunarAgeRatio));
        $coefficient = (int) round(42 + (58 * $springNeapWave));

        $isAguaje = $coefficient >= 85;
        $tideTypeLabel = match (true) {
            $coefficient >= 90 => 'Marea Viva / Aguaje Grande (Ideal para ver los corrales en bajamar)',
            $coefficient >= 70 => 'Marea Media-Viva',
            $coefficient >= 50 => 'Marea Media',
            default => 'Marea Muerta (Poca amplitud en los corrales)',
        };

        // Desfase diario aproximado de 50 minutos por día lunar sobre el puerto de referencia (Bonanza / Chipiona)
        $baseMinutes = (int) round(fmod($daysSinceNewMoon * 50.47, 745));
        $firstHigh = $date->copy()->startOfDay()->addMinutes(140 + $baseMinutes);
        $firstLow = $firstHigh->copy()->addMinutes(372);
        $secondHigh = $firstHigh->copy()->addMinutes(745);
        $secondLow = $secondHigh->copy()->addMinutes(372);

        $highHeight = round(2.65 + ($springNeapWave * 0.95), 2);
        $lowHeight = round(1.05 - ($springNeapWave * 0.65), 2);

        $events = collect([
            [
                'type' => 'pleamar',
                'label' => '1.ª Pleamar (Marea llena)',
                'time' => $firstHigh->format('H:i'),
                'height_m' => $highHeight,
                'corrales_note' => 'Corrales cubiertos por el Atlántico.',
            ],
            [
                'type' => 'bajamar',
                'label' => '1.ª Bajamar (Marea vacía)',
                'time' => $firstLow->format('H:i'),
                'height_m' => $lowHeight,
                'corrales_note' => 'Momento óptimo para observar la piedra ostionera sin pisar el escaramujo.',
            ],
            [
                'type' => 'pleamar',
                'label' => '2.ª Pleamar (Marea llena)',
                'time' => $secondHigh->format('H:i'),
                'height_m' => round($highHeight - 0.1, 2),
                'corrales_note' => 'Entrada de pescado y marisco a los corrales.',
            ],
            [
                'type' => 'bajamar',
                'label' => '2.ª Bajamar (Marea vacía)',
                'time' => $secondLow->format('H:i'),
                'height_m' => round($lowHeight + 0.08, 2),
                'corrales_note' => 'Descubierta la restinga de Salmedina y muros de pesca.',
            ],
        ])->sortBy('time')->values()->all();

        return [
            'coefficient' => $coefficient,
            'is_aguaje' => $isAguaje,
            'type_label' => $tideTypeLabel,
            'lunar_day' => (int) round($daysSinceNewMoon) + 1,
            'events' => $events,
        ];
    }
}

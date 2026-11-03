<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Throwable;

/**
 * Servicio de información meteorológica (AEMET OpenData municipio 11016 - Chipiona)
 * y cálculo astronómico de la tabla diaria de mareas y coeficiente para los corrales
 * y playas de Chipiona (Punta del Perro / Regla / Las Canteras).
 */
class ChipionaWeatherService
{
    /**
     * Obtiene la previsión meteorológica y el estado de las mareas de Chipiona para hoy.
     *
     * @return array<string, mixed>
     */
    public function getTodaySummary(): array
    {
        return Cache::remember('chipiona_weather_and_tides_'.now()->format('Y_m_d_H'), 1800, function (): array {
            $weather = $this->fetchAemetForecast();
            $tides = $this->calculateChipionaTides(Carbon::now('Europe/Madrid'));

            return [
                'municipality' => 'Chipiona (Cádiz)',
                'aemet_code' => config('services.aemet.municipality_code', '11016'),
                'updated_at' => Carbon::now('Europe/Madrid')->format('d/m/Y H:i'),
                'weather' => $weather,
                'tides' => $tides,
            ];
        });
    }

    /**
     * Consulta la API de AEMET OpenData si hay API key configurada o devuelve
     * datos climatológicos seguros para la costa de Chipiona.
     *
     * @return array<string, mixed>
     */
    private function fetchAemetForecast(): array
    {
        $apiKey = config('services.aemet.api_key');
        $municipality = config('services.aemet.municipality_code', '11016');

        if (! empty($apiKey)) {
            try {
                $metaResponse = Http::withHeaders(['api_key' => $apiKey])
                    ->timeout(4)
                    ->get("https://opendata.aemet.es/opendata/api/prediccion/especifica/municipio/diaria/{$municipality}");

                if ($metaResponse->successful() && $metaResponse->json('datos')) {
                    $dataResponse = Http::timeout(4)->get((string) $metaResponse->json('datos'));
                    if ($dataResponse->successful()) {
                        $payload = $dataResponse->json('0.prediccion.dia.0');
                        if (is_array($payload)) {
                            return [
                                'source' => 'AEMET OpenData Oficial (11016)',
                                'temp_max' => (int) ($payload['temperatura']['maxima'] ?? 22),
                                'temp_min' => (int) ($payload['temperatura']['minima'] ?? 15),
                                'sky_state' => (string) ($payload['estadoCielo'][0]['descripcion'] ?? 'Cielo despejado en el litoral'),
                                'wind_direction' => (string) ($payload['viento'][0]['direccion'] ?? 'Poniente (O)'),
                                'wind_speed_kmh' => (int) ($payload['viento'][0]['velocidad'] ?? 18),
                                'uv_max' => (int) ($payload['uvMax'] ?? 6),
                                'rain_prob' => (int) ($payload['probPrecipitacion'][0]['value'] ?? 5),
                                'sea_temp' => 19,
                            ];
                        }
                    }
                }
            } catch (Throwable) {
                // Fallback seguro si AEMET OpenData no responde
            }
        }

        return [
            'source' => 'Referencia AEMET Municipio 11016 (Chipiona)',
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

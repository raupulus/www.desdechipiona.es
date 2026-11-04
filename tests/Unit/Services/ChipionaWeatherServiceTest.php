<?php

namespace Tests\Unit\Services;

use App\Services\ChipionaWeatherService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChipionaWeatherServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_get_today_summary_returns_fallback_when_endpoint_fails(): void
    {
        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $service = new ChipionaWeatherService;
        $summary = $service->getTodaySummary();

        $this->assertEquals('Chipiona (Cádiz)', $summary['municipality']);
        $this->assertNull($summary['aemet_code']);
        $this->assertArrayHasKey('weather', $summary);
        $this->assertArrayHasKey('tides', $summary);
        $this->assertStringContainsString('Chipiona', $summary['weather']['source']);
        $this->assertEquals(22, $summary['weather']['temp_max']);
        $this->assertEquals(15, $summary['weather']['temp_min']);
    }

    public function test_get_today_summary_fetches_and_caches_remote_microservice_data(): void
    {
        config(['services.raupulus_weather.token' => 'test-bearer-token-123']);

        Http::fake([
            'https://api.raupulus.dev/*' => Http::response([
                'success' => true,
                'message' => 'Operación exitosa',
                'data' => [
                    'municipality' => 'Chipiona (Cádiz)',
                    'province' => 'Cádiz',
                    'aemet_code' => '11015',
                    'updated_at' => '04/11/2026 16:30',
                    'updated_at_iso' => now()->toIso8601String(),
                    'weather' => [
                        'source' => 'Estación Meteorológica Chipiona + AEMET OpenData (11015)',
                        'current_temp' => 20.8,
                        'temp_max' => 26,
                        'temp_min' => 18,
                        'sky_state' => 'Despejado',
                        'wind_direction' => 'Poniente',
                        'wind_speed_kmh' => 20,
                        'uv_max' => 7,
                        'rain_prob' => 0,
                    ],
                    'tides' => [
                        'coefficient' => 91,
                        'is_aguaje' => true,
                        'type_label' => 'Marea Viva / Aguaje Grande (Ideal para ver los corrales en bajamar)',
                        'lunar_day' => 14,
                        'events' => [
                            [
                                'type' => 'bajamar',
                                'label' => '1.ª Bajamar (Marea vacía)',
                                'time' => '09:58',
                                'height_m' => 0.52,
                                'corrales_note' => 'Momento óptimo para observar la piedra ostionera sin pisar el escaramujo.',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new ChipionaWeatherService;
        $summary = $service->getTodaySummary();

        Http::assertSent(function ($request) {
            return $request->hasHeader('Authorization', 'Bearer test-bearer-token-123')
                && $request->hasHeader('Accept', 'application/json');
        });

        $this->assertEquals('Estación Meteorológica Chipiona + AEMET OpenData (11015)', $summary['weather']['source']);
        $this->assertEquals(20.8, $summary['weather']['current_temp']);
        $this->assertEquals(26, $summary['weather']['temp_max']);
        $this->assertEquals(18, $summary['weather']['temp_min']);
        $this->assertEquals('Despejado', $summary['weather']['sky_state']);
        $this->assertTrue($summary['tides']['is_aguaje']);
        $this->assertEquals(91, $summary['tides']['coefficient']);
        $this->assertEquals('11015', $summary['aemet_code']);
    }

    public function test_calculate_chipiona_tides_returns_valid_structure(): void
    {
        $service = new ChipionaWeatherService;
        $date = Carbon::create(2026, 8, 15, 12, 0, 0, 'Europe/Madrid');

        $tides = $service->calculateChipionaTides($date);

        $this->assertArrayHasKey('coefficient', $tides);
        $this->assertArrayHasKey('is_aguaje', $tides);
        $this->assertArrayHasKey('type_label', $tides);
        $this->assertArrayHasKey('lunar_day', $tides);
        $this->assertArrayHasKey('events', $tides);

        $this->assertGreaterThanOrEqual(38, $tides['coefficient']);
        $this->assertLessThanOrEqual(105, $tides['coefficient']);
        $this->assertCount(4, $tides['events']);

        foreach ($tides['events'] as $event) {
            $this->assertContains($event['type'], ['pleamar', 'bajamar']);
            $this->assertNotEmpty($event['time']);
            $this->assertIsFloat($event['height_m']);
            $this->assertNotEmpty($event['corrales_note']);
        }
    }
}

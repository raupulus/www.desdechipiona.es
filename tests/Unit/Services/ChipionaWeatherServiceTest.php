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
        $this->assertEquals('11016', $summary['aemet_code']);
        $this->assertArrayHasKey('weather', $summary);
        $this->assertArrayHasKey('tides', $summary);
        $this->assertStringContainsString('Chipiona (11016)', $summary['weather']['source']);
        $this->assertEquals(22, $summary['weather']['temp_max']);
        $this->assertEquals(15, $summary['weather']['temp_min']);
    }

    public function test_get_today_summary_fetches_and_caches_remote_microservice_data(): void
    {
        Http::fake([
            'https://api.raupulus.dev/*' => Http::response([
                'municipality' => 'Chipiona (Cádiz)',
                'aemet_code' => '11016',
                'updated_at' => now()->toIso8601String(),
                'weather' => [
                    'source' => 'api.raupulus.dev / AEMET 11016',
                    'temp_max' => 26,
                    'temp_min' => 18,
                    'sky_state' => 'Despejado',
                    'wind_direction' => 'Poniente',
                    'wind_speed_kmh' => 20,
                    'uv_max' => 7,
                    'rain_prob_percent' => 0,
                ],
                'tides' => [
                    'source' => 'IHM / Sanlúcar-Chipiona',
                    'coefficient' => 85,
                    'is_aguaje' => true,
                    'type_label' => 'Mareas vivas (aguajes de luna)',
                    'today_events' => [],
                ],
            ], 200),
        ]);

        $service = new ChipionaWeatherService;
        $summary = $service->getTodaySummary();

        $this->assertEquals('api.raupulus.dev / AEMET 11016', $summary['weather']['source']);
        $this->assertEquals(26, $summary['weather']['temp_max']);
        $this->assertEquals(18, $summary['weather']['temp_min']);
        $this->assertEquals('Despejado', $summary['weather']['sky_state']);
        $this->assertTrue($summary['tides']['is_aguaje']);
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

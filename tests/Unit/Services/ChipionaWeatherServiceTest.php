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

    public function test_get_today_summary_returns_fallback_when_no_api_key(): void
    {
        config(['services.aemet.api_key' => '']);

        $service = new ChipionaWeatherService;
        $summary = $service->getTodaySummary();

        $this->assertEquals('Chipiona (Cádiz)', $summary['municipality']);
        $this->assertEquals('11016', $summary['aemet_code']);
        $this->assertArrayHasKey('weather', $summary);
        $this->assertArrayHasKey('tides', $summary);
        $this->assertStringContainsString('Referencia AEMET', $summary['weather']['source']);
        $this->assertEquals(22, $summary['weather']['temp_max']);
        $this->assertEquals(15, $summary['weather']['temp_min']);
    }

    public function test_get_today_summary_fetches_and_decodes_aemet_forecast(): void
    {
        config(['services.aemet.api_key' => 'test-api-key']);
        config(['services.aemet.municipality_code' => '11016']);

        Http::fake([
            'opendata.aemet.es/opendata/api/prediccion/especifica/municipio/diaria/11016' => Http::response([
                'estado' => 200,
                'datos' => 'https://opendata.aemet.es/mock-data-url',
            ], 200),
            'opendata.aemet.es/mock-data-url' => Http::response([
                [
                    'prediccion' => [
                        'dia' => [
                            [
                                'temperatura' => ['maxima' => 26, 'minima' => 18],
                                'estadoCielo' => [['descripcion' => 'Despejado']],
                                'viento' => [['direccion' => 'Poniente', 'velocidad' => 20]],
                                'uvMax' => 7,
                                'probPrecipitacion' => [['value' => 0]],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $service = new ChipionaWeatherService;
        $summary = $service->getTodaySummary();

        $this->assertEquals('AEMET OpenData Oficial (11016)', $summary['weather']['source']);
        $this->assertEquals(26, $summary['weather']['temp_max']);
        $this->assertEquals(18, $summary['weather']['temp_min']);
        $this->assertEquals('Despejado', $summary['weather']['sky_state']);
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

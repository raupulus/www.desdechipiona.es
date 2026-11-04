<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\ChipionaWeatherService;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;

/**
 * Controlador público para la página de El Tiempo y Mareas (AEMET 11016 - Chipiona).
 */
class WeatherController extends Controller
{
    /**
     * Muestra la previsión meteorológica de AEMET y la tabla diaria de mareas de Chipiona.
     */
    public function index(ChipionaWeatherService $weatherService, SeoService $seo): View
    {
        $summary = $weatherService->getTodaySummary();
        $coastalPages = Page::publishedAndActive()
            ->whereIn('content_group', ['playas', 'monumentos'])
            ->limit(4)
            ->get();

        $seo->setTitle(__('messages.weather_tides_nav').' - '.setting('site_title', config('app.name')))
            ->setDescription(__('messages.weather_seo_description'))
            ->setUrl(route('weather.index'));

        return view('weather.index', compact('summary', 'coastalPages', 'seo'));
    }
}

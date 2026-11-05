<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\Proverb;
use App\Models\Section;
use App\Services\ContentSearchService;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Controlador para la página de inicio y explorador principal de Desde Chipiona.
 * Integra el buscador multicriterio (por apartado, época, rango de años, rigor documental y mapa GPS),
 * junto con las secciones dinámicas, slides, últimas noticias y refrán destacado.
 */
class HomeController extends Controller
{
    /**
     * Muestra la página principal con el explorador facetado de Chipiona.
     */
    public function index(Request $request, ContentSearchService $searchService, SeoService $seo): View
    {
        $filters = $request->only([
            'q',
            'group',
            'era',
            'evidence',
            'category',
            'year_from',
            'year_to',
            'map',
            'sort',
        ]);

        $pages = $searchService->searchPages($filters, 12);
        if ($pages->currentPage() > 1 && $pages->isEmpty()) {
            abort(404);
        }
        $groupCounts = $searchService->getGroupCounts();

        $sectionIds = Cache::remember('sections_home_ids', 3600, fn () => Section::forZone('home')->pluck('id')->all());
        $sections = Section::whereIn('id', $sectionIds)->orderBy('order')->get();

        $latestNews = News::publishedAndActive()
            ->with(['categories:id,slug,name', 'relatedPages:id,slug,title'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $featuredProverb = Proverb::approved()
            ->with('relatedPage:id,slug,title')
            ->orderByDesc('featured')
            ->latest()
            ->first();

        $siteTitle = (string) setting('site_title', config('app.name'));
        $currentPage = $pages->currentPage();
        $baseTitle = $siteTitle.' — '.__('messages.site_tagline');
        $title = $currentPage > 1
            ? $baseTitle.' ('.__('messages.page_number', ['page' => $currentPage]).')'
            : $baseTitle;
        $baseDescription = app()->getLocale() === 'en'
            ? __('messages.about_seo_description')
            : (string) setting('site_description', __('messages.about_seo_description'));
        $description = $currentPage > 1
            ? $baseDescription.' ('.__('messages.page_number', ['page' => $currentPage]).')'
            : $baseDescription;

        $seo->setTitle($title)
            ->setDescription($description)
            ->setUrl(route('home'));

        return view('home', compact(
            'pages',
            'filters',
            'groupCounts',
            'sections',
            'latestNews',
            'featuredProverb',
            'seo'
        ));
    }
}

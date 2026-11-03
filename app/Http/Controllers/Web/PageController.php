<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;

/**
 * Controlador para las páginas enciclopédicas y estáticas del frontend.
 * Carga las páginas enlazadas (directas e inversas), noticias relacionadas y comentarios aprobados.
 */
class PageController extends Controller
{
    /**
     * Muestra el detalle de una página dinámica por su slug.
     * Devuelve 404 si la página no existe, no está publicada o no está activa.
     */
    public function show(string $slug, SeoService $seo): View
    {
        $page = Page::where('slug', $slug)
            ->publishedAndActive()
            ->with([
                'categories',
                'relatedPages' => fn ($q) => $q->publishedAndActive(),
                'linkedFromPages' => fn ($q) => $q->publishedAndActive(),
                'news' => fn ($q) => $q->publishedAndActive()->limit(4),
                'comments' => fn ($q) => $q->approved(),
            ])
            ->firstOrFail();

        $allLinkedPages = $page->relatedPages
            ->merge($page->linkedFromPages)
            ->unique('id')
            ->values();

        $pageUrl = route('pages.show', $page->slug);
        $seo->fromModel($page)
            ->setType('article')
            ->setAuthor('Raúl Caro Pastorino')
            ->setUrl($pageUrl)
            ->setBreadcrumbs([
                ['name' => __('messages.home'), 'url' => route('home')],
                ['name' => $page->title, 'url' => $pageUrl],
            ]);

        if (in_array($page->slug, ['privacy', 'cookies', 'terms', 'legal-notice'], true)) {
            $seo->setRobots('noindex,follow');
        }

        return view('pages.show', compact('page', 'allLinkedPages', 'seo'));
    }
}

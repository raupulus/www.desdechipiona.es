<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * Controlador público para la sección independiente de Noticias y Hallazgos Documentales de Chipiona.
 */
class NewsController extends Controller
{
    /**
     * Muestra el listado paginado de noticias publicadas con filtro opcional por categoría o búsqueda.
     */
    public function index(Request $request, SeoService $seo): View
    {
        $query = News::publishedAndActive()
            ->with(['categories', 'relatedPages' => fn ($q) => $q->publishedAndActive()])
            ->orderByDesc('featured')
            ->orderByDesc('published_at');

        $search = trim((string) $request->query('q', ''));
        if ($search !== '') {
            $likeOp = $query->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
            $term = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($sub) use ($likeOp, $term): void {
                $sub->where('title', $likeOp, $term)
                    ->orWhere('excerpt', $likeOp, $term)
                    ->orWhere('content', $likeOp, $term)
                    ->orWhere('location_name', $likeOp, $term);
            });
        }

        $categorySlug = trim((string) $request->query('category', ''));
        if ($categorySlug !== '') {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $categorySlug));
        }

        $newsItems = $query->paginate(9)->withQueryString();
        $categories = Category::active()->ordered()->get();

        $seo->setTitle('Noticias y Hallazgos Documentales - '.setting('site_title', config('app.name')))
            ->setDescription('Actualidad cultural, nuevos legajos incorporados y crónicas históricas sobre Chipiona.')
            ->setUrl(route('news.index'));

        return view('news.index', compact('newsItems', 'categories', 'search', 'categorySlug', 'seo'));
    }

    /**
     * Muestra el detalle completo de una noticia por su slug.
     */
    public function show(string $slug, SeoService $seo): View
    {
        $news = News::where('slug', $slug)
            ->publishedAndActive()
            ->with([
                'categories',
                'relatedPages' => fn ($q) => $q->publishedAndActive(),
                'comments' => fn ($q) => $q->approved(),
            ])
            ->firstOrFail();

        $newsUrl = route('news.show', $news->slug);
        $seo->fromModel($news)
            ->setType('article')
            ->setAuthor('Raúl Caro Pastorino')
            ->setUrl($newsUrl)
            ->setBreadcrumbs([
                ['name' => __('messages.home'), 'url' => route('home')],
                ['name' => 'Noticias', 'url' => route('news.index')],
                ['name' => $news->title, 'url' => $newsUrl],
            ]);

        return view('news.show', compact('news', 'seo'));
    }
}

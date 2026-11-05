<?php

namespace App\Http\Controllers\Web;

use App\Enums\SuggestionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ProverbSubmissionRequest;
use App\Models\Page;
use App\Models\Proverb;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Controlador público del Refranero Popular de Chipiona.
 * Permite consultar y filtrar los dichos marineros y agrícolas, así como subir nuevos refranes.
 */
class ProverbController extends Controller
{
    /**
     * Muestra el refranero popular y el formulario para subir refranes.
     */
    public function index(Request $request, SeoService $seo): View
    {
        $category = (string) $request->query('category', '');
        $search = mb_substr(trim((string) $request->query('q', '')), 0, 120);

        $query = Proverb::approved()
            ->with('relatedPage:id,slug,title')
            ->orderByDesc('featured')
            ->latest();

        if ($category !== '' && $category !== 'all') {
            $query->where('category', $category);
        }

        if ($search !== '') {
            $likeOp = $query->getConnection()->getDriverName() === 'pgsql' ? 'ILIKE' : 'LIKE';
            $term = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($sub) use ($likeOp, $term): void {
                $sub->where('text', $likeOp, $term)
                    ->orWhere('meaning', $likeOp, $term)
                    ->orWhere('author_name', $likeOp, $term);
            });
        }

        $proverbs = $query->paginate(12)->withQueryString();
        $pages = Page::publishedAndActive()->orderBy('slug')->get(['id', 'slug', 'title']);

        $currentPage = $proverbs->currentPage();
        $siteTitle = (string) setting('site_title', config('app.name'));
        $title = $currentPage > 1
            ? __('messages.proverbs_nav').' ('.__('messages.page_number', ['page' => $currentPage]).') — '.$siteTitle
            : __('messages.proverbs_nav').' — '.$siteTitle;
        $description = $currentPage > 1
            ? __('messages.proverbs_seo_description').' ('.__('messages.page_number', ['page' => $currentPage]).')'
            : __('messages.proverbs_seo_description');

        $seo->setTitle($title)
            ->setDescription($description)
            ->setUrl(route('proverbs.index'))
            ->setBreadcrumbs([
                ['name' => __('messages.home'), 'url' => route('home')],
                ['name' => __('messages.proverbs_nav'), 'url' => route('proverbs.index')],
            ]);

        return view('proverbs.index', compact('proverbs', 'pages', 'category', 'search', 'seo'));
    }

    /**
     * Registra un nuevo refrán enviado por un vecino (pendiente de moderación).
     */
    public function store(ProverbSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (! empty($validated['related_page_id'])) {
            Page::publishedAndActive()->findOrFail((int) $validated['related_page_id']);
        }

        /** @var array<string, string|null>|null $social */
        $social = $request->session()->get('public_social_identity');
        $user = auth()->user();

        Proverb::create([
            'text' => $validated['text'],
            'meaning' => $validated['meaning'] ?? null,
            'category' => $validated['category'],
            'source_code' => $validated['source_code'] ?? 'Tradición oral vecinal',
            'related_page_id' => $validated['related_page_id'] ?? null,
            'author_name' => $social['name'] ?? $user?->name ?? 'Vecino Verificado',
            'author_email' => $social['email'] ?? $user?->email ?? null,
            'oauth_provider' => $social['provider'] ?? ($user ? 'local' : null),
            'oauth_id' => $social['id'] ?? ($user ? (string) $user->id : null),
            'status' => SuggestionStatus::Pending,
            'featured' => false,
        ]);

        return redirect()
            ->route('proverbs.index')
            ->with('success', __('messages.proverb_sent_success'));
    }
}

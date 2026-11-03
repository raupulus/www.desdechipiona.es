<?php

namespace App\Http\Controllers\Web;

use App\Enums\ContentGroup;
use App\Enums\SuggestionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\ContentSuggestionSubmissionRequest;
use App\Models\ContentSuggestion;
use App\Models\Page;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

/**
 * Controlador público para el formulario de sugerencias de nuevos contenidos sobre Chipiona.
 */
class ContentSuggestionController extends Controller
{
    /**
     * Muestra el formulario para proponer nuevos contenidos (historia, monumentos, recetas, refranes...).
     */
    public function create(SeoService $seo): View
    {
        $pages = Page::publishedAndActive()->orderBy('slug')->get(['id', 'slug', 'title']);
        $groups = ContentGroup::cases();

        $seo->setTitle('Aportar Contenido sobre Chipiona - '.setting('site_title', config('app.name')))
            ->setDescription('Comparte historias documentadas, fotografías antiguas, recetas marineras o recuerdos de Chipiona.')
            ->setUrl(route('suggestions.create'));

        return view('suggestions.create', compact('pages', 'groups', 'seo'));
    }

    /**
     * Almacena la sugerencia de contenido enviada por un visitante.
     */
    public function store(ContentSuggestionSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        /** @var array<string, string|null>|null $social */
        $social = $request->session()->get('public_social_identity');

        ContentSuggestion::create([
            'content_group' => $validated['content_group'],
            'title' => $validated['title'],
            'related_page_id' => $validated['related_page_id'] ?? null,
            'body' => $validated['body'],
            'sources_info' => $validated['sources_info'] ?? null,
            'location_name' => $validated['location_name'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'author_name' => $social['name'] ?? ($validated['author_name'] ?? null) ?: 'Colaborador de Chipiona',
            'author_email' => $social['email'] ?? ($validated['author_email'] ?? null),
            'oauth_provider' => $social['provider'] ?? null,
            'oauth_id' => $social['id'] ?? null,
            'author_avatar' => $social['avatar'] ?? null,
            'status' => SuggestionStatus::Pending,
        ]);

        return redirect()
            ->route('suggestions.create')
            ->with('success', __('messages.suggestion_sent_success'));
    }
}

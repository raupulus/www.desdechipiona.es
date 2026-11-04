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

        $seo->setTitle(__('messages.contribute_btn').' - '.setting('site_title', config('app.name')))
            ->setDescription(__('messages.suggestions_seo_description'))
            ->setUrl(route('suggestions.create'));

        return view('suggestions.create', compact('pages', 'groups', 'seo'));
    }

    /**
     * Almacena la sugerencia de contenido enviada por un visitante.
     */
    public function store(ContentSuggestionSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (! empty($validated['related_page_id'])) {
            Page::publishedAndActive()->findOrFail((int) $validated['related_page_id']);
        }

        /** @var array<string, string|null>|null $social */
        $social = $request->session()->get('public_social_identity');
        $user = auth()->user();

        ContentSuggestion::create([
            'content_group' => $validated['content_group'],
            'title' => $validated['title'],
            'related_page_id' => $validated['related_page_id'] ?? null,
            'body' => $validated['body'],
            'sources_info' => $validated['sources_info'] ?? null,
            'location_name' => $validated['location_name'] ?? null,
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'author_name' => $social['name'] ?? $user?->name ?? 'Colaborador Verificado',
            'author_email' => $social['email'] ?? $user?->email ?? null,
            'oauth_provider' => $social['provider'] ?? ($user ? 'local' : null),
            'oauth_id' => $social['id'] ?? ($user ? (string) $user->id : null),
            'author_avatar' => $social['avatar'] ?? null,
            'status' => SuggestionStatus::Pending,
        ]);

        return redirect()
            ->route('suggestions.create')
            ->with('success', __('messages.suggestion_sent_success'));
    }
}

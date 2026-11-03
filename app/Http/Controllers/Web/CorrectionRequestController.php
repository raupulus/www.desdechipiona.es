<?php

namespace App\Http\Controllers\Web;

use App\Enums\SuggestionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CorrectionSubmissionRequest;
use App\Models\CorrectionRequest;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;

/**
 * Controlador público para recibir solicitudes de correcciones sobre páginas o noticias.
 */
class CorrectionRequestController extends Controller
{
    /**
     * Almacena una propuesta de corrección vinculada a una Page o News.
     */
    public function store(CorrectionSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        /** @var array<string, string|null>|null $social */
        $social = $request->session()->get('public_social_identity');

        $target = $validated['correctable_type'] === 'news'
            ? News::publishedAndActive()->findOrFail((int) $validated['correctable_id'])
            : Page::publishedAndActive()->findOrFail((int) $validated['correctable_id']);

        CorrectionRequest::create([
            'correctable_type' => $target->getMorphClass(),
            'correctable_id' => $target->getKey(),
            'quoted_text' => $validated['quoted_text'] ?? null,
            'proposed_correction' => $validated['proposed_correction'],
            'sources_info' => $validated['sources_info'] ?? null,
            'author_name' => $social['name'] ?? ($validated['author_name'] ?? null) ?: 'Lector de Desde Chipiona',
            'author_email' => $social['email'] ?? ($validated['author_email'] ?? null),
            'oauth_provider' => $social['provider'] ?? null,
            'oauth_id' => $social['id'] ?? null,
            'author_avatar' => $social['avatar'] ?? null,
            'status' => SuggestionStatus::Pending,
        ]);

        return redirect()
            ->back()
            ->with('success', __('messages.correction_sent_success'));
    }
}

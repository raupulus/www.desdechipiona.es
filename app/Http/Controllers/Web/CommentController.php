<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Web\CommentSubmissionRequest;
use App\Models\Comment;
use App\Models\News;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;

/**
 * Controlador público para registrar comentarios en páginas enciclopédicas y noticias.
 */
class CommentController extends Controller
{
    /**
     * Almacena un comentario público asociado a una Page o News.
     */
    public function store(CommentSubmissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        /** @var array<string, string|null>|null $social */
        $social = $request->session()->get('public_social_identity');

        $targetClass = $validated['commentable_type'] === 'news' ? News::class : Page::class;
        $target = $validated['commentable_type'] === 'news'
            ? News::publishedAndActive()->findOrFail((int) $validated['commentable_id'])
            : Page::publishedAndActive()->findOrFail((int) $validated['commentable_id']);

        Comment::create([
            'commentable_type' => $target->getMorphClass(),
            'commentable_id' => $target->getKey(),
            'body' => $validated['body'],
            'author_name' => $social['name'] ?? ($validated['author_name'] ?? null) ?: 'Vecino de Chipiona',
            'author_email' => $social['email'] ?? ($validated['author_email'] ?? null),
            'oauth_provider' => $social['provider'] ?? null,
            'oauth_id' => $social['id'] ?? null,
            'author_avatar' => $social['avatar'] ?? null,
            'is_approved' => false,
        ]);

        return redirect()
            ->back()
            ->with('success', __('messages.comment_sent_success'));
    }
}

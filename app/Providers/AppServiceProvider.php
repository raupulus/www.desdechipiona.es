<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Comment;
use App\Models\ContentSuggestion;
use App\Models\CorrectionRequest;
use App\Models\News;
use App\Models\OfficialPhone;
use App\Models\Page;
use App\Models\Post;
use App\Models\Proverb;
use App\Models\Section;
use App\Models\SentEmail;
use App\Models\Setting;
use App\Models\Slide;
use App\Models\User;
use App\Policies\CategoryPolicy;
use App\Policies\CommentPolicy;
use App\Policies\ContentSuggestionPolicy;
use App\Policies\CorrectionRequestPolicy;
use App\Policies\NewsPolicy;
use App\Policies\OfficialPhonePolicy;
use App\Policies\PagePolicy;
use App\Policies\PostPolicy;
use App\Policies\ProverbPolicy;
use App\Policies\SectionPolicy;
use App\Policies\SentEmailPolicy;
use App\Policies\SettingPolicy;
use App\Policies\SlidePolicy;
use App\Policies\UserPolicy;
use App\Services\SeoService;
use App\Services\SettingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Translatable\Translatable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(SeoService::class);
        $this->app->singleton(SettingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        // Configuración de Spatie Translatable: fallback a español y fallbackAny
        // para garantizar que si un contenido aún no está traducido al inglés
        // se muestre siempre la versión documental en español sin dejar huecos vacíos.
        app(Translatable::class)
            ->fallback(
                fallbackLocale: (string) config('app.fallback_locale', 'es'),
                fallbackAny: true
            );

        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Page::class, PagePolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Slide::class, SlidePolicy::class);
        Gate::policy(Section::class, SectionPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(SentEmail::class, SentEmailPolicy::class);
        Gate::policy(News::class, NewsPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(CorrectionRequest::class, CorrectionRequestPolicy::class);
        Gate::policy(ContentSuggestion::class, ContentSuggestionPolicy::class);
        Gate::policy(Proverb::class, ProverbPolicy::class);
        Gate::policy(OfficialPhone::class, OfficialPhonePolicy::class);
    }
}

<?php

namespace Tests\Feature\Admin;

use App\Enums\Role;
use App\Filament\Admin\Resources\CommentResource;
use App\Filament\Admin\Resources\ContentSuggestionResource;
use App\Filament\Admin\Resources\CorrectionRequestResource;
use App\Filament\Admin\Resources\NewsResource;
use App\Filament\Admin\Resources\OfficialPhoneResource;
use App\Filament\Admin\Resources\PageResource;
use App\Filament\Admin\Resources\ProverbResource;
use App\Filament\Admin\Resources\RelationManagers\CorrectionRequestsRelationManager;
use App\Models\Comment;
use App\Models\ContentSuggestion;
use App\Models\CorrectionRequest;
use App\Models\News;
use App\Models\OfficialPhone;
use App\Models\Page;
use App\Models\Proverb;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Valida el acceso a los nuevos recursos Filament de Desde Chipiona en /admin
 * (Noticias, Correcciones, Sugerencias, Refranes, Comentarios, Teléfonos Oficiales y
 * la pestaña «Sugerencias del contenido» en Páginas y Noticias).
 */
class ChipionaAdminResourcesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => Role::SuperAdmin,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_access_all_chipiona_resources(): void
    {
        $page = Page::factory()->create();
        $news = News::factory()->create();
        $correction = CorrectionRequest::factory()->create([
            'correctable_type' => Page::class,
            'correctable_id' => $page->id,
        ]);
        $suggestion = ContentSuggestion::factory()->create();
        $proverb = Proverb::factory()->create();
        $comment = Comment::factory()->create();
        $phone = OfficialPhone::factory()->create();

        $this->actingAs($this->admin)
            ->get(PageResource::getUrl('edit', ['record' => $page]))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(NewsResource::getUrl('index'))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(NewsResource::getUrl('edit', ['record' => $news]))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(CorrectionRequestResource::getUrl('index'))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(CorrectionRequestResource::getUrl('edit', ['record' => $correction]))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(ContentSuggestionResource::getUrl('index'))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(ContentSuggestionResource::getUrl('edit', ['record' => $suggestion]))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(ProverbResource::getUrl('index'))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(ProverbResource::getUrl('edit', ['record' => $proverb]))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(CommentResource::getUrl('index'))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(CommentResource::getUrl('edit', ['record' => $comment]))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(OfficialPhoneResource::getUrl('index'))
            ->assertSuccessful();

        $this->actingAs($this->admin)
            ->get(OfficialPhoneResource::getUrl('edit', ['record' => $phone]))
            ->assertSuccessful();
    }

    public function test_page_and_news_resources_register_content_suggestions_relation_manager(): void
    {
        $this->assertContains(CorrectionRequestsRelationManager::class, PageResource::getRelations());
        $this->assertContains(CorrectionRequestsRelationManager::class, NewsResource::getRelations());
    }
}

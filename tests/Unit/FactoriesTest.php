<?php

namespace Tests\Unit;

use App\Enums\ContentGroup;
use App\Enums\ContentStatus;
use App\Enums\EvidenceLevel;
use App\Enums\HistoricalEra;
use App\Enums\Role;
use App\Enums\SuggestionStatus;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ContentSuggestion;
use App\Models\CorrectionRequest;
use App\Models\News;
use App\Models\OfficialPhone;
use App\Models\Page;
use App\Models\Proverb;
use App\Models\Setting;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Database\Factories\PageFactory;
use Database\Factories\PostCategoryFactory;
use Database\Factories\PostFactory;
use Database\Factories\SocialMediaFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_factory_states(): void
    {
        $published = PostFactory::new()->published()->create();
        $this->assertEquals(ContentStatus::Published, $published->status);
        $this->assertTrue($published->is_active);
        $this->assertNotNull($published->published_at);

        $draft = PostFactory::new()->draft()->create();
        $this->assertEquals(ContentStatus::Draft, $draft->status);
        $this->assertNull($draft->published_at);

        $featured = PostFactory::new()->featured()->create();
        $this->assertTrue($featured->featured);
    }

    public function test_page_factory_states_and_modular_content(): void
    {
        $active = PageFactory::new()->active()->create();
        $this->assertTrue($active->is_active);

        $inactive = PageFactory::new()->inactive()->create();
        $this->assertFalse($inactive->is_active);

        $modular = PageFactory::new()->withModularBlocks()->create();
        $contentEs = $modular->getTranslation('content', 'es');
        $this->assertIsArray($contentEs);
        $this->assertEquals('text', $contentEs[0]['type']);
        $this->assertEquals('cta', $contentEs[1]['type']);
    }

    public function test_post_category_factory(): void
    {
        $category = PostCategoryFactory::new()->active()->create();
        $this->assertInstanceOf(Category::class, $category);
        $this->assertNotEmpty($category->slug);
        $this->assertNotEmpty($category->getTranslation('name', 'es'));
        $this->assertNotEmpty($category->getTranslation('name', 'en'));
        $this->assertTrue($category->is_active);
    }

    public function test_social_media_factory(): void
    {
        $social = SocialMediaFactory::new()->active()->create();
        $this->assertInstanceOf(Setting::class, $social);
        $this->assertEquals('social', $social->group);
        $this->assertNotEmpty($social->value);
        $this->assertStringStartsWith('https://', $social->value);

        $inactiveSocial = SocialMediaFactory::new()->inactive()->create();
        $this->assertNull($inactiveSocial->value);
    }

    public function test_user_factory_states(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $this->assertEquals(Role::SuperAdmin, $superadmin->role);

        $admin = User::factory()->admin()->create();
        $this->assertEquals(Role::Admin, $admin->role);

        $inactive = User::factory()->inactive()->create();
        $this->assertFalse($inactive->is_active);
    }

    public function test_category_factory_states(): void
    {
        $active = CategoryFactory::new()->active()->create();
        $this->assertTrue($active->is_active);

        $inactive = CategoryFactory::new()->inactive()->create();
        $this->assertFalse($inactive->is_active);
    }

    public function test_chipiona_models_and_factories(): void
    {
        $page = Page::factory()->withCoordinates()->create([
            'content' => ['es' => "## El Faro de Chipiona\n\nConstruido por Jaime Font en **1867**.", 'en' => '## Chipiona Lighthouse'],
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $relatedPage = Page::factory()->create();
        $page->relatedPages()->attach($relatedPage->id);

        $this->assertTrue($page->hasCoordinates());
        $this->assertEquals('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', $page->youtube_embed_url);
        $this->assertStringContainsString('<h2>El Faro de Chipiona</h2>', $page->rendered_content);
        $this->assertEquals(ContentGroup::Historia, $page->content_group);
        $this->assertEquals(HistoricalEra::Contemporary, $page->historical_era);
        $this->assertEquals(EvidenceLevel::Consolidated, $page->evidence_level);
        $this->assertCount(1, $page->relatedPages);

        $news = News::factory()->published()->create();
        $news->relatedPages()->attach($page->id);
        $this->assertCount(1, $news->relatedPages);
        $this->assertStringContainsString('<h2>Hallazgo en el archivo de Chipiona</h2>', $news->rendered_content);

        $correction = CorrectionRequest::factory()->create([
            'correctable_type' => Page::class,
            'correctable_id' => $page->id,
        ]);
        $this->assertEquals(SuggestionStatus::Pending, $correction->status);
        $this->assertCount(1, $page->correctionRequests);

        $suggestion = ContentSuggestion::factory()->create(['related_page_id' => $page->id]);
        $this->assertEquals($page->id, $suggestion->relatedPage->id);

        $proverb = Proverb::factory()->approved()->create(['related_page_id' => $page->id]);
        $this->assertEquals(SuggestionStatus::Approved, $proverb->status);
        $this->assertEquals(1, Proverb::approved()->count());

        $comment = Comment::factory()->create([
            'commentable_type' => Page::class,
            'commentable_id' => $page->id,
        ]);
        $this->assertTrue($comment->is_approved);
        $this->assertCount(1, $page->comments);

        $phone = OfficialPhone::factory()->create();
        $this->assertEquals('956929060', $phone->tel_href);
        $this->assertEquals(1, OfficialPhone::active()->ordered()->count());
    }
}

<?php

namespace Tests\Feature\Api;

use App\Enums\ContentStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_published_and_active_posts(): void
    {
        $user = User::factory()->create();

        Post::create([
            'title' => ['es' => 'Post Publicado', 'en' => 'Published Post'],
            'slug' => 'post-publicado',
            'status' => ContentStatus::Published,
            'is_active' => true,
            'user_id' => $user->id,
            'published_at' => now()->subDay(),
        ]);

        Post::create([
            'title' => ['es' => 'Post Borrador', 'en' => 'Draft Post'],
            'slug' => 'post-borrador',
            'status' => ContentStatus::Draft,
            'is_active' => true,
            'user_id' => $user->id,
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/v1/posts');

        $response->assertOk();
        $response->assertJsonPath('total', 1);
        $response->assertJsonFragment(['slug' => 'post-publicado']);
        $response->assertJsonMissing(['slug' => 'post-borrador']);
    }

    public function test_show_returns_post_by_slug(): void
    {
        $user = User::factory()->create();

        Post::create([
            'title' => ['es' => 'Faro de Chipiona', 'en' => 'Chipiona Lighthouse'],
            'slug' => 'faro-de-chipiona',
            'content' => ['es' => 'Contenido sobre el faro', 'en' => 'Content about lighthouse'],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'user_id' => $user->id,
            'image' => 'posts/faro.jpg',
            'published_at' => now()->subDay(),
        ]);

        $response = $this->getJson('/api/v1/posts/faro-de-chipiona');

        $response->assertOk();
        $response->assertJsonFragment(['slug' => 'faro-de-chipiona']);
        $response->assertJsonFragment(['image' => 'posts/faro.jpg']);
        $response->assertJsonStructure([
            'id',
            'title',
            'slug',
            'excerpt',
            'content',
            'image',
            'image_url',
            'status',
            'published_at',
            'author',
            'categories',
        ]);
    }

    public function test_show_returns_404_for_nonexistent_or_draft_post(): void
    {
        $user = User::factory()->create();

        Post::create([
            'title' => ['es' => 'Post Oculto'],
            'slug' => 'post-oculto',
            'status' => ContentStatus::Draft,
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $this->getJson('/api/v1/posts/post-oculto')->assertNotFound();
        $this->getJson('/api/v1/posts/inexistente')->assertNotFound();
    }
}

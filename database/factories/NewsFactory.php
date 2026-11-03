<?php

namespace Database\Factories;

use App\Enums\ContentStatus;
use App\Enums\EvidenceLevel;
use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factoría para el modelo News.
 *
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'title' => ['es' => fake()->sentence(), 'en' => fake()->sentence()],
            'excerpt' => ['es' => fake()->sentence(), 'en' => fake()->sentence()],
            'content' => [
                'es' => "## Hallazgo en el archivo de Chipiona\n\n".fake()->paragraphs(3, true),
                'en' => "## Discovery in the Chipiona archive\n\n".fake()->paragraphs(3, true),
            ],
            'evidence_level' => EvidenceLevel::Documented,
            'status' => ContentStatus::Published,
            'is_active' => true,
            'featured' => false,
            'user_id' => User::factory(),
            'published_at' => now(),
        ];
    }

    /**
     * Estado publicado y activo.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Published,
            'is_active' => true,
            'published_at' => now(),
        ]);
    }

    /**
     * Estado en borrador.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Draft,
            'published_at' => null,
        ]);
    }

    /**
     * Estado inactivo.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

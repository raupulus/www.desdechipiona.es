<?php

namespace Database\Factories;

use App\Enums\ContentGroup;
use App\Enums\ContentStatus;
use App\Enums\EvidenceLevel;
use App\Enums\HistoricalEra;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factoría para el modelo Page con soporte para metadatos de Desde Chipiona.
 *
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

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
            'content' => ['es' => '<p>'.fake()->paragraphs(3, true).'</p>', 'en' => '<p>'.fake()->paragraphs(3, true).'</p>'],
            'excerpt' => ['es' => fake()->sentence(), 'en' => fake()->sentence()],
            'content_group' => ContentGroup::Historia,
            'historical_era' => HistoricalEra::Contemporary,
            'period_label' => 'Siglo XIX',
            'year_start' => 1863,
            'year_end' => 1867,
            'evidence_level' => EvidenceLevel::Consolidated,
            'evidence_note' => [
                'es' => 'Información contrastada con fuentes primarias de archivo.',
                'en' => 'Information verified against primary archive sources.',
            ],
            'source_references' => [
                ['title' => 'Archivo Histórico Provincial de Cádiz', 'author' => 'Junta de Andalucía', 'year' => '2026', 'url' => ''],
            ],
            'status' => ContentStatus::Published,
            'is_active' => true,
            'featured' => false,
            'user_id' => User::factory(),
            'published_at' => now(),
        ];
    }

    /**
     * Estado activo.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
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

    /**
     * Estado publicado.
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
     * Estado borrador.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ContentStatus::Draft,
            'published_at' => null,
        ]);
    }

    /**
     * Estado con coordenadas GPS de Chipiona.
     */
    public function withCoordinates(float $lat = 36.7378, float $lng = -6.4425, string $location = 'Punta del Perro, Chipiona'): static
    {
        return $this->state(fn (array $attributes) => [
            'latitude' => $lat,
            'longitude' => $lng,
            'location_name' => $location,
        ]);
    }

    /**
     * Genera contenido con bloques modulares del Page Builder.
     */
    public function withModularBlocks(): static
    {
        return $this->state(fn (array $attributes) => [
            'content' => [
                'es' => [
                    [
                        'type' => 'text',
                        'data' => ['content' => '<p>'.fake()->paragraph().'</p>'],
                    ],
                    [
                        'type' => 'cta',
                        'data' => [
                            'title' => 'Soluciones a Medida',
                            'description' => 'Contáctanos para más información.',
                            'button_text' => 'Contactar',
                            'button_url' => '/contact',
                        ],
                    ],
                ],
                'en' => [
                    [
                        'type' => 'text',
                        'data' => ['content' => '<p>'.fake()->paragraph().'</p>'],
                    ],
                    [
                        'type' => 'cta',
                        'data' => [
                            'title' => 'Custom Solutions',
                            'description' => 'Contact us for further details.',
                            'button_text' => 'Contact Us',
                            'button_url' => '/contact',
                        ],
                    ],
                ],
            ],
        ]);
    }
}

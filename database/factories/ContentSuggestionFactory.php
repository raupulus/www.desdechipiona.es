<?php

namespace Database\Factories;

use App\Enums\ContentGroup;
use App\Enums\SuggestionStatus;
use App\Models\ContentSuggestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factoría para el modelo ContentSuggestion.
 *
 * @extends Factory<ContentSuggestion>
 */
class ContentSuggestionFactory extends Factory
{
    protected $model = ContentSuggestion::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content_group' => ContentGroup::Historia,
            'title' => fake()->sentence(6),
            'body' => fake()->paragraphs(3, true),
            'sources_info' => 'Fotografía familiar de los años 50 y recorte del Diario de Cádiz.',
            'location_name' => 'Calle Isaac Peral, Chipiona',
            'latitude' => 36.7365,
            'longitude' => -6.4352,
            'author_name' => fake()->name(),
            'author_email' => fake()->safeEmail(),
            'oauth_provider' => 'facebook',
            'oauth_id' => (string) fake()->randomNumber(8, true),
            'status' => SuggestionStatus::Pending,
        ];
    }

    /**
     * Estado aprobado.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SuggestionStatus::Approved,
        ]);
    }
}

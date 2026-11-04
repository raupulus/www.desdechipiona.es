<?php

namespace Database\Factories;

use App\Enums\SuggestionStatus;
use App\Models\CorrectionRequest;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factoría para el modelo CorrectionRequest.
 *
 * @extends Factory<CorrectionRequest>
 */
class CorrectionRequestFactory extends Factory
{
    protected $model = CorrectionRequest::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'correctable_type' => Page::class,
            'correctable_id' => Page::factory(),
            'quoted_text' => 'En 1865 se inauguró el faro.',
            'proposed_correction' => 'El encendido oficial de la linterna del Faro de Chipiona tuvo lugar el 28 de noviembre de 1867.',
            'sources_info' => 'Archivo de la Autoridad Portuaria de Sevilla.',
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
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Estado rechazado.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SuggestionStatus::Rejected,
            'reviewed_at' => now(),
        ]);
    }
}

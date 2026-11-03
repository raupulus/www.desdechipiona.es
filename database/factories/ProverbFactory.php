<?php

namespace Database\Factories;

use App\Enums\SuggestionStatus;
use App\Models\Proverb;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factoría para el modelo Proverb (Refranes de Chipiona).
 *
 * @extends Factory<Proverb>
 */
class ProverbFactory extends Factory
{
    protected $model = Proverb::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text' => 'Con marea de aguaje, el corral se sale y el marisco se coge a puñados.',
            'meaning' => 'En las grandes mareas vivas (aguajes), la bajamar deja al descubierto toda la piedra ostionera de los corrales de pesca.',
            'category' => 'mar',
            'source_code' => 'Tradición oral corralera',
            'author_name' => fake()->name(),
            'author_email' => fake()->safeEmail(),
            'status' => SuggestionStatus::Approved,
            'featured' => false,
        ];
    }

    /**
     * Estado pendiente de moderación.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => SuggestionStatus::Pending,
        ]);
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

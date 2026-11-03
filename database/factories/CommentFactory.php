<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factoría para el modelo Comment.
 *
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commentable_type' => Page::class,
            'commentable_id' => Page::factory(),
            'body' => 'Recuerdo cuando mi abuelo nos llevaba a ver los corrales con la marea vacía.',
            'author_name' => fake()->name(),
            'author_email' => fake()->safeEmail(),
            'oauth_provider' => 'facebook',
            'oauth_id' => (string) fake()->randomNumber(8, true),
            'is_approved' => true,
        ];
    }

    /**
     * Estado no aprobado.
     */
    public function unapproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_approved' => false,
        ]);
    }
}

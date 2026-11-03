<?php

namespace Database\Factories;

use App\Models\OfficialPhone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factoría para el modelo OfficialPhone.
 *
 * @extends Factory<OfficialPhone>
 */
class OfficialPhoneFactory extends Factory
{
    protected $model = OfficialPhone::class;

    /**
     * Define el estado por defecto del modelo.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Policía Local de Chipiona',
            'category' => 'emergencias',
            'phone' => '956 92 90 60',
            'secondary_phone' => '112',
            'address' => 'Plaza Juan Carlos I, s/n, 11550 Chipiona',
            'schedule' => '24 horas (todos los días del año)',
            'description' => 'Atención ciudadana, seguridad vial y urgencias municipales.',
            'latitude' => 36.7367,
            'longitude' => -6.4351,
            'is_emergency' => true,
            'is_active' => true,
            'sort_order' => 1,
        ];
    }
}

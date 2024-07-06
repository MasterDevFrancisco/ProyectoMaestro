<?php

namespace Database\Factories;

use App\Models\Campos;
use Illuminate\Database\Eloquent\Factories\Factory;

class CamposFactory extends Factory
{
    protected $model = Campos::class;

    public function definition()
    {
        return [
            'tablas_id' => \App\Models\Tablas::factory(),
            'nombre_columna' => $this->faker->word,
            'status' => $this->faker->randomElement(['activo', 'inactivo']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

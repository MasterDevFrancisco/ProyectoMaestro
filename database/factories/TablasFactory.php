<?php

namespace Database\Factories;

use App\Models\Tablas;
use Illuminate\Database\Eloquent\Factories\Factory;

class TablasFactory extends Factory
{
    protected $model = Tablas::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->word,
            'elementos_id' => \App\Models\Elementos::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

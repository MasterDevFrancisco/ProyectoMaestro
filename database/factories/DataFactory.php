<?php

namespace Database\Factories;

use App\Models\Data;
use Illuminate\Database\Eloquent\Factories\Factory;

class DataFactory extends Factory
{
    protected $model = Data::class;

    public function definition()
    {
        return [
            'rowID' => $this->faker->uuid,
            'valor' => $this->faker->word,
            'campos_id' => \App\Models\Campos::factory(),
            'users_id' => \App\Models\User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}


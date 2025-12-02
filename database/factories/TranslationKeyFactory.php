<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationKeyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'key' => 'key_' . $this->faker->unique()->uuid(),
            'context' => $this->faker->sentence(3),
        ];
    }
}

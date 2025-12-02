<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\TranslationKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'translation_key_id' => TranslationKey::factory(),
            'language_id'        => Language::inRandomOrder()->first()->id ?? 1,
            'value'              => $this->faker->sentence(),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'fileable_type' => 'App\Models\User',
            'fileable_id' => User::factory(),
            'path' => $this->faker->imageUrl(),
            'name' => $this->faker->word(),
            'type' => $this->faker->randomElement(['image', 'video', 'document']),
            'size' => $this->faker->numberBetween(1000, 1000000),
        ];
    }
}

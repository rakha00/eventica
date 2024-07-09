<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventPackage>
 */
class EventPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = fake()->text(20),
            'price' => fake()->randomNumber(5, true),
            'description' => fake()->text(50),
            'capacity' => $capacity = fake()->randomNumber(1, true),
            'remaining' => $capacity,
            'slug' => Str::slug($title),
        ];
    }
}

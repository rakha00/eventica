<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $uri = 'https://rakha00.github.io/api-wilayah-indonesia/api/provinces.json';
        $provinces = json_decode(file_get_contents($uri), true);

        $provinceOptions = collect($provinces)->mapWithKeys(function ($province) {
            return [$province['name'] => $province['name']];
        });

        return [
            'title' => $title = fake()->text(20),
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'highlight' => fake()->paragraph(),
            'image' => fake()->imageUrl(),
            'start_event' => $startEvent = fake()->dateTimeBetween('now', '+1 month'),
            'end_event' => fake()->dateTimeBetween($startEvent, '+1 month'),
            'location' => $provinceOptions->random(),
            'status' => fake()->randomElement(['published', 'unpublished']),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => '11',
            'event_package_id' => fake()->numberBetween(1, 10),
            'order_id' => uniqid(),
            'quantity' => 0,
            'total_price' => 300000,
            'status' => fake()->randomElement(['Pending', 'Completed', 'Canceled']),
        ];
    }
}

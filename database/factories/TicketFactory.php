<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => uniqid(),
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone' => '08' . mt_rand(100000000, 999999999),
            'identity_card_number' => mt_rand(1000000000000, 9999999999999),
            'status' => 'Inactive',
        ];
    }
}

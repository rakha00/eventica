<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::factory(3)->create()->each(function ($transaction) {
            $numberOfTickets = fake()->numberBetween(1, 5); // Generate a random number of tickets between 1 and 5
            $transaction->update(['quantity' => $numberOfTickets]); // Update the quantity in the transaction
            $tickets = Ticket::factory($numberOfTickets)->make();
            $transaction->tickets()->saveMany($tickets);

            if ($transaction->status === 'Completed') {
                $tickets->each(function ($ticket) {
                    $ticket->update(['status' => 'Active']); // Update the status of the ticket to active if transaction status is completed
                });
            }
        });
    }
}

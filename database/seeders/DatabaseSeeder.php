<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventCategory;
use App\Models\EventPackage;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Post::factory(10)->create();
        $categories = EventCategory::factory(5)->create();
        Event::factory(10)->make()->each(function ($event) use ($categories) {
            $event->event_category_id = $categories->random()->id;
            $event->save();

            EventPackage::factory(3)->create([
                'start_valid' => fake()->dateTimeBetween($event->start_event, $event->end_event),
                'end_valid' => fake()->dateTimeBetween($event->start_event, $event->end_event),
                'event_id' => $event->id,
            ]);
        });


        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('qwerty123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('qwerty123'),
            'role' => 'user',
        ]);
    }
}
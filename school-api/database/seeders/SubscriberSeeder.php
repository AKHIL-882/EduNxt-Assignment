<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscriber;
use Faker\Factory as Faker;

class SubscriberSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 50) as $index) {
            Subscriber::create([
                'email' => $faker->unique()->safeEmail,
            ]);
        }
    }
}

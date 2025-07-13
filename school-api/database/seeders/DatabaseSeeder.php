<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\ProductSeeder;
use Database\Seeders\UserTypeSeeder;
use Database\Seeders\SubscriberSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         $this->call([UserTypeSeeder::class]);
         $this->call([
             GradeSeeder::class,
             AttendanceSeeder::class,
         ]);
         $this->call([
            ProductSeeder::class,
        ]);
        $this->call([
            SubscriberSeeder::class,
        ]);
    }
}

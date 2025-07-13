<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class UserTypeSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // 1 admin (type = 0)
        User::create([
            'name' => $faker->name(),
            'email' => 'admin_' . Str::random(5) . '@example.com',
            'password' => Hash::make('password'),
            'type' => 0, // Admin
        ]);

        // 2 teachers (type = 1)
        for ($i = 1; $i <= 2; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => 'teacher_' . Str::random(5) . "@example.com",
                'password' => Hash::make('password'),
                'type' => 1, // Teacher
            ]);
        }

        // 10 students (type = 2)
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => 'student_' . Str::random(6) . '@example.com',
                'password' => Hash::make('password'),
                'type' => 2, // Student
            ]);
        }
    }
}

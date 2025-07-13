<?php
namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;
    public function definition()
    {
        return [
            'student_id' => User::factory(),
            'date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['Present', 'Absent']),
        ];
    }
}

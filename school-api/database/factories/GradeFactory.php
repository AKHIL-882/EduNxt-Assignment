<?php
namespace Database\Factories;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;
    public function definition()
    {
        return [
            'student_id' => User::factory(),
            'subject' => $this->faker->randomElement(['Telugu', 'Hindi', 'English', 'Math', 'Science', 'Social']),
            'grade' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'F']),
        ];
    }
}

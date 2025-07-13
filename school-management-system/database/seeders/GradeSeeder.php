<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;
use App\Models\User;

class GradeSeeder extends Seeder
{
    public function run()
    {
        $subjects = ['Telugu', 'Hindi','English','Math', 'Science', 'Social'];
        $students = User::where('type', 2)->get();
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                Grade::factory()->create([
                    'student_id' => $student->id,
                    'subject' => $subject,
                ]);
            }
        }
    }
}

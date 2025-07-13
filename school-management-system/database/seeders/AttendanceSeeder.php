<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        $students = User::where('type', 2)->get();
        foreach ($students as $student) {
            Attendance::factory()->count(10)->create(['student_id' => $student->id]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grade;
use App\Models\Attendance;
use App\Enums\UserTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    public function getStudentGrades()
    {
        // Only allow teachers
        if (Auth::user()->type !== UserTypeEnum::TEACHER) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $grades = Grade::with('student:id,name')->get();
        $result = $grades->map(function ($g) {
            return [
                'id' => $g->student->id,
                'name' => $g->student->name,
                'subject' => $g->subject,
                'grade' => $g->grade
            ];
        });
        return response()->json($result);
    }

    public function getStudentAttendance()
    {
        if (Auth::user()->type !== UserTypeEnum::TEACHER) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $attendance = Attendance::with('student:id,name')->get();
        $result = $attendance->map(function ($a) {
            return [
                'id' => $a->student->id,
                'name' => $a->student->name,
                'date' => $a->date,
                'status' => $a->status
            ];
        });
        return response()->json($result);
    }

    public function getProfile()
    {
        if (Auth::user()->type !== UserTypeEnum::TEACHER) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $user = Auth::user();
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function updateGrade(Request $request)
    {
        $user = Auth::user();
        if ($user->type !== UserTypeEnum::TEACHER) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'student_id' => 'required|integer',
            'subject' => 'required|string',
            'grade' => 'required|string',
        ]);

        $grade = Grade::where('student_id', $request->student_id)
            ->where('subject', $request->subject)
            ->firstOrFail();

        $grade->grade = $request->grade;
        $grade->save();

        return response()->json(['success' => true, 'grade' => $grade]);
    }

    public function updateAttendance(Request $request)
    {
        $user = Auth::user();
        if ($user->type !== UserTypeEnum::TEACHER) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'student_id' => 'required|integer',
            'date' => 'required|date',
            'status' => 'required|string|in:Present,Absent',
        ]);

        $attendance = Attendance::where('student_id', $request->student_id)
            ->where('date', $request->date)
            ->firstOrFail();

        $attendance->status = $request->status;
        $attendance->save();

        return response()->json(['success' => true, 'attendance' => $attendance]);
    }

    public function getPermissions()
    {
        $user = Auth::user();
        $permissions = $user->getAllPermissions()->pluck('name');
        return response()->json($permissions);
    }
}

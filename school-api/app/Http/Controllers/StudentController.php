<?php
namespace App\Http\Controllers;

use App\Enums\UserTypeEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Grade;

class StudentController extends Controller
{
    public function getProfile()
    {
        $user = Auth::user();
        info($user);
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        if ($user->type != UserTypeEnum::STUDENT) {
            return response()->json(['error' => 'Unauthorized: role'], 403);
        }
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function getGrades()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }
        if ($user->type != UserTypeEnum::STUDENT) {
            return response()->json(['error' => 'Unauthorized: role'], 403);
        }
        $grades = Grade::where('student_id', $user->id)->get(['subject', 'grade']);
        return response()->json($grades);
    }
}

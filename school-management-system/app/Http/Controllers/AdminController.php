<?php
namespace App\Http\Controllers;

use App\Enums\UserTypeEnum;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as ModelsRole;

class AdminController extends Controller
{
    public function getProfile()
    {
        if (Auth::user()->type !== UserTypeEnum::ADMIN) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        $user = Auth::user();
        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function listTeachers() {
        $teachers = User::where('type', 1)->get();
        return response()->json($teachers);
    }
    public function createTeacher(Request $request) {
        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 1,
        ]);
        return response()->json($teacher);
    }
    public function updateTeacher(Request $request, $id) {
        $teacher = User::where('type', 1)->findOrFail($id);
        $teacher->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $teacher->password,
        ]);
        return response()->json($teacher);
    }
    public function deleteTeacher($id) {
        $teacher = User::where('type', 1)->findOrFail($id);
        $teacher->delete();
        return response()->json(['success' => true]);
    }

    public function listStudents() {
        $students = User::where('type', 2)->get();
        return response()->json($students);
    }
    public function createStudent(Request $request) {
        $student = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => 2,
            'teacher_id' => $request->teacher_id,
        ]);
        return response()->json($student);
    }
    public function updateStudent(Request $request, $id) {
        $student = User::where('type', 2)->findOrFail($id);
        $student->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $student->password,
            'teacher_id' => $request->teacher_id ?? $student->teacher_id,
        ]);
        return response()->json($student);
    }
    public function deleteStudent($id) {
        $student = User::where('type', 2)->findOrFail($id);
        $student->delete();
        return response()->json(['success' => true]);
    }

    public function listRoles() {
        return response()->json(ModelsRole::with('permissions')->get());
    }
    public function createRole(Request $request) {
        $role = Role::create(['name' => $request->name]);
        return response()->json($role);
    }
    public function updateRole(Request $request, $id) {
        $role = Role::findOrFail($id);
        $role->update(['name' => $request->name]);
        return response()->json($role);
    }
    public function deleteRole($id) {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(['success' => true]);
    }
    public function listPermissions() {
        return response()->json(Permission::all());
    }
    public function assignPermission(Request $request, $id) {
        // $id is role_id
        $role = DB::table('roles')->where('id', $id)->first();
        $permission = DB::table('permissions')->where('id', $request->permission_id)->first();

        if (!$role || !$permission) {
            return response()->json(['error' => 'Role or Permission not found.'], 404);
        }

        DB::table('role_has_permissions')->updateOrInsert([
            'role_id' => $id,
            'permission_id' => $request->permission_id
        ]);

        return response()->json(['success' => true]);
    }
    public function revokePermission(Request $request, $id) {
        // $id is role_id
        $role = DB::table('roles')->where('id', $id)->first();
        $permission = DB::table('permissions')->where('id', $request->permission_id)->first();

        if (!$role || !$permission) {
            return response()->json(['error' => 'Role or Permission not found.'], 404);
        }

        DB::table('role_has_permissions')
            ->where('role_id', $id)
            ->where('permission_id', $request->permission_id)
            ->delete();

        return response()->json(['success' => true]);
    }
    public function listUsers() {
        return response()->json(User::with('role')->get());
    }
    public function updateUserRole(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->type = $request->role_id;
        $user->save();
        return response()->json(['success' => true, 'user' => $user]);
    }
}

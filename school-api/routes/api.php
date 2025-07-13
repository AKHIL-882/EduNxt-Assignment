<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ForgetPasswordController;
use App\Http\Controllers\Admin\NewsletterDashboardController;
use App\Http\Controllers\CreateRolesAndPermissionsController;

Route::middleware(['auth:api', 'api'])->group(function () {
    // Teacher endpoints
    Route::get('/teacher/profile', [TeacherController::class, 'getProfile']);
    Route::get('/teacher/grades', [TeacherController::class, 'getStudentGrades']);
    Route::get('/teacher/attendance', [TeacherController::class, 'getStudentAttendance']);
    Route::post('/teacher/grades/{id}', [TeacherController::class, 'updateGrade']);
    Route::post('/teacher/attendance/{id}', [TeacherController::class, 'updateAttendance']);
    Route::get('/teacher/permissions', [TeacherController::class, 'getPermissions']);


    // Student endpoints
    Route::get('/student/profile', [StudentController::class, 'getProfile']);
    Route::get('/student/grades', [StudentController::class, 'getGrades']);

    // Admin endpoints
    Route::get('/admin/profile', [AdminController::class, 'getProfile']);
    // Admin CRUD endpoints
    Route::get('/admin/teachers', [AdminController::class, 'listTeachers']);
    Route::post('/admin/teachers', [AdminController::class, 'createTeacher']);
    Route::put('/admin/teachers/{id}', [AdminController::class, 'updateTeacher']);
    Route::delete('/admin/teachers/{id}', [AdminController::class, 'deleteTeacher']);

    Route::get('/admin/students', [AdminController::class, 'listStudents']);
    Route::post('/admin/students', [AdminController::class, 'createStudent']);
    Route::put('/admin/students/{id}', [AdminController::class, 'updateStudent']);
    Route::delete('/admin/students/{id}', [AdminController::class, 'deleteStudent']);

    // Admin Roles & Permissions endpoints
    Route::get('/admin/roles', [AdminController::class, 'listRoles']);
    Route::post('/admin/roles', [AdminController::class, 'createRole']);
    Route::put('/admin/roles/{id}', [AdminController::class, 'updateRole']);
    Route::delete('/admin/roles/{id}', [AdminController::class, 'deleteRole']);
    Route::get('/admin/permissions', [AdminController::class, 'listPermissions']);
    Route::post('/admin/roles/{id}/assign-permission', [AdminController::class, 'assignPermission']);
    Route::post('/admin/roles/{id}/revoke-permission', [AdminController::class, 'revokePermission']);
    Route::get('/admin/users', [AdminController::class, 'listUsers']);
    Route::put('/admin/users/{id}/role', [AdminController::class, 'updateUserRole']);

    Route::get('/admin/products', [OrderController::class, 'index']);
    Route::post('/admin/orders', [OrderController::class, 'store']);

    Route::post('/newsletter/send', [NewsletterController::class, 'send']);

    Route::get('/admin/newsletter-dashboard', [NewsletterDashboardController::class, 'dashboard'])
     ->name('admin.newsletter.dashboard');

});

Route::middleware(['guest', 'throttle:10,1'])->group(function () {

    Route::post('reset-password', [ForgetPasswordController::class, 'resetPassword']);
    Route::post('update-password', [ForgetPasswordController::class, 'updatePassword']);
    Route::post('signup', [AuthenticationController::class, 'signup']);
    Route::post('login', [AuthenticationController::class, 'login']);
    Route::post('logout', [AuthenticationController::class, 'logout']);

    Route::get('create-roles', [CreateRolesAndPermissionsController::class, 'createRoles']);
});

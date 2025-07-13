<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CreateRolesAndPermissionsController extends Controller
{
    public function createRoles()
    {
        try {
            $roles = ['admin', 'teacher', 'student'];

            foreach ($roles as $role) {
                Role::firstOrCreate(['name' => $role]);
            }

            return ApiResponse::setMessage('Admin, Teacher, and Student roles created successfully')
                ->response(Response::HTTP_OK);
        } catch (Throwable $e) {
            return ApiResponse::setMessage($e->getMessage())
                ->response(Response::HTTP_BAD_REQUEST);
        }
    }
}

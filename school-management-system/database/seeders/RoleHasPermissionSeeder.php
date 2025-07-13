<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionSeeder extends Seeder
{
    public function run()
    {
        // Fetch role IDs by name
        $roles = DB::table('roles')->pluck('id', 'name');
        // Fetch permission IDs by name
        $permissions = DB::table('permissions')->pluck('id', 'name');

        $rolePermissions = [
            // Admin gets all permissions
            ['role' => 'Admin', 'permission' => 'manage_users'],
            ['role' => 'Admin', 'permission' => 'manage_grades'],
            ['role' => 'Admin', 'permission' => 'manage_attendance'],
            ['role' => 'Admin', 'permission' => 'manage_courses'],
            ['role' => 'Admin', 'permission' => 'view_dashboard'],
            ['role' => 'Admin', 'permission' => 'create-posts'],
            ['role' => 'Admin', 'permission' => 'edit-users'],
            ['role' => 'Admin', 'permission' => 'delete-comments'],
            // Teacher gets grades, attendance, dashboard, create-posts
            ['role' => 'Teacher', 'permission' => 'manage_grades'],
            ['role' => 'Teacher', 'permission' => 'manage_attendance'],
            ['role' => 'Teacher', 'permission' => 'view_dashboard'],
            ['role' => 'Teacher', 'permission' => 'create-posts'],
            // Student gets dashboard only
            ['role' => 'Student', 'permission' => 'view_dashboard'],
        ];
        foreach ($rolePermissions as $rp) {
            $role_id = $roles[$rp['role']] ?? null;
            $permission_id = $permissions[$rp['permission']] ?? null;
            if ($role_id && $permission_id) {
                DB::table('role_has_permissions')->updateOrInsert([
                    'role_id' => $role_id,
                    'permission_id' => $permission_id
                ], [
                    'role_id' => $role_id,
                    'permission_id' => $permission_id
                ]);
            }
        }
    }
}

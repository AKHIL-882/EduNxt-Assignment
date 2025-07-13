<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            ['name' => 'manage_users', 'guard_name' => 'web'],
            ['name' => 'manage_grades', 'guard_name' => 'web'],
            ['name' => 'manage_attendance', 'guard_name' => 'web'],
            ['name' => 'manage_courses', 'guard_name' => 'web'],
            ['name' => 'view_dashboard', 'guard_name' => 'web'],
            ['name' => 'create-posts', 'guard_name' => 'web'],
            ['name' => 'edit-users', 'guard_name' => 'web'],
            ['name' => 'delete-comments', 'guard_name' => 'web'],
        ];
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(['name' => $permission['name']], $permission);
        }
    }
}
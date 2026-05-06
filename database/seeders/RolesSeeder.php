<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'users.manage', 'users.ban',
            'tasks.moderate', 'tasks.hide',
            'verifications.review',
            'disputes.resolve',
            'reviews.moderate',
            'reference.manage',
            'finance.view',
        ];

        foreach ($permissions as $name) {
            Permission::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        $roles = [
            'super_admin' => $permissions,
            'moderator' => [
                'tasks.moderate', 'tasks.hide',
                'verifications.review',
                'disputes.resolve',
                'reviews.moderate',
                'users.ban',
            ],
            'finance' => [
                'finance.view',
                'disputes.resolve',
            ],
            'content_manager' => [
                'reference.manage',
                'tasks.hide',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }
}

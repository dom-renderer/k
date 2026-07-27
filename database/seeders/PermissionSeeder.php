<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // System permissions grouped by module
        $permissionGroups = [
            'Coating Case Management' => [
                'case-list',
                'case-create',
                'case-edit',
                'case-delete',
                'case-approve',
                'case-download',
            ],
            'Sector Management' => [
                'sector-list',
                'sector-create',
                'sector-edit',
                'sector-delete',
            ],
            'Equipment Management' => [
                'equipment-list',
                'equipment-create',
                'equipment-edit',
                'equipment-delete',
            ],
            'User Management' => [
                'user-list',
                'user-create',
                'user-edit',
                'user-delete',
            ],
            'Role Management' => [
                'role-list',
                'role-create',
                'role-edit',
                'role-delete',
            ],
            'Activity Log Management' => [
                'activity-log-list',
            ],
            'Setting Management' => [
                'setting-list',
                'setting-edit',
            ],
            'User Guide & Documentation' => [
                'doc-list',
            ],
        ];

        // Delete obsolete permissions
        Permission::whereIn('name', [
            'inventory-list', 'inventory-create', 'inventory-edit', 'inventory-delete',
            'report-list', 'report-export'
        ])->delete();

        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        // Create Roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'sector-list',
            'equipment-list',
            'case-list',
            'case-create',
            'case-edit',
            'case-download',
            'doc-list',
        ]);

        // Assign Admin role to default admin user
        $adminUser = User::where('username', 'admin')->first();
        if ($adminUser) {
            $adminUser->assignRole($adminRole);
        }
    }
}

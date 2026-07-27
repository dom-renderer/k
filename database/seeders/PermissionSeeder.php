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
            'Setting Management' => [
                'setting-list',
                'setting-edit',
            ],
            'Inventory Management' => [
                'inventory-list',
                'inventory-create',
                'inventory-edit',
                'inventory-delete',
            ],
            'Reports' => [
                'report-list',
                'report-export',
            ],
        ];

        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permissionName) {
                Permission::firstOrCreate(['name' => $permissionName]);
            }
        }

        // Create Roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions(['user-list', 'sector-list', 'equipment-list', 'inventory-list', 'report-list']);

        // Assign Admin role to default admin user
        $adminUser = User::where('username', 'admin')->first();
        if ($adminUser) {
            $adminUser->assignRole($adminRole);
        }
    }
}

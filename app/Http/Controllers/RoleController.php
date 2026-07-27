<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles or handle DataTables AJAX request using Yajra DataTables.
     */
    public function index(Request $request)
    {
        abort_if(!auth()->user()->can('role-list'), 403, 'Unauthorized action.');

        if ($request->ajax()) {
            $roles = Role::withCount(['permissions', 'users']);

            return DataTables::of($roles)
                ->addColumn('name', function ($role) {
                    return '<div class="d-flex align-items-center gap-2">
                        <span class="badge bg-light-primary text-primary p-2 rounded"><i class="ti ti-shield fs-5"></i></span>
                        <div>
                            <div class="fw-semibold text-dark">' . e(ucfirst($role->name)) . '</div>
                            <div class="small text-muted">Guard: ' . e($role->guard_name) . '</div>
                        </div>
                    </div>';
                })
                ->addColumn('permissions_count', function ($role) {
                    return '<span class="badge bg-secondary">' . ($role->permissions_count ?? 0) . ' Permissions</span>';
                })
                ->addColumn('users_count', function ($role) {
                    return '<span class="badge bg-info">' . ($role->users_count ?? 0) . ' Users</span>';
                })
                ->addColumn('actions', function ($role) {
                    $actionsHtml = '';
                    if (auth()->user()->can('role-edit')) {
                        $actionsHtml .= '<a href="' . route('roles.edit', $role->id) . '" class="text-body"><i class="ti ti-edit fs-5"></i></a>';
                    }
                    if (auth()->user()->can('role-delete') && $role->name !== 'admin') {
                        $actionsHtml .= ' <a href="javascript:void(0)" class="link-danger delete-role-btn ms-2" data-id="' . $role->id . '" data-name="' . e($role->name) . '"><i class="ti ti-trash fs-5"></i></a>';
                    }
                    return $actionsHtml ?: '<span class="text-muted small">None</span>';
                })
                ->rawColumns(['name', 'permissions_count', 'users_count', 'actions'])
                ->make(true);
        }

        return view('roles.index');
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        abort_if(!auth()->user()->can('role-create'), 403, 'Unauthorized action.');

        $groupedPermissions = $this->getGroupedPermissions();
        return view('roles.create', compact('groupedPermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('role-create'), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create(['name' => strtolower($validated['name'])]);
        $role->syncPermissions($validated['permissions']);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        abort_if(!auth()->user()->can('role-edit'), 403, 'Unauthorized action.');

        $groupedPermissions = $this->getGroupedPermissions();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role)
    {
        abort_if(!auth()->user()->can('role-edit'), 403, 'Unauthorized action.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update(['name' => strtolower($validated['name'])]);
        $role->syncPermissions($validated['permissions']);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role from storage.
     */
    public function destroy(Role $role)
    {
        abort_if(!auth()->user()->can('role-delete'), 403, 'Unauthorized action.');

        if ($role->name === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'The super admin role cannot be deleted.',
            ], 400);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully.',
        ]);
    }

    /**
     * Helper to group permissions by module.
     */
    protected function getGroupedPermissions()
    {
        $groups = [
            'User Management' => ['user-list', 'user-create', 'user-edit', 'user-delete'],
            'Role Management' => ['role-list', 'role-create', 'role-edit', 'role-delete'],
            'Sector Management' => ['sector-list', 'sector-create', 'sector-edit', 'sector-delete'],
            'Equipment Management' => ['equipment-list', 'equipment-create', 'equipment-edit', 'equipment-delete'],
            'Setting Management' => ['setting-list', 'setting-edit'],
            'Inventory Management' => ['inventory-list', 'inventory-create', 'inventory-edit', 'inventory-delete'],
            'Reports' => ['report-list', 'report-export'],
        ];

        $allPermissions = Permission::all()->keyBy('name');
        $grouped = [];

        foreach ($groups as $groupName => $permissionNames) {
            $grouped[$groupName] = [];
            foreach ($permissionNames as $pName) {
                if ($allPermissions->has($pName)) {
                    $grouped[$groupName][] = $allPermissions->get($pName);
                }
            }
        }

        return $grouped;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the users or handle DataTables AJAX request using Yajra DataTables.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::with('roles')->select('users.*');

            return DataTables::of($query)
                ->addColumn('name', function ($user) {
                    return '<a href="#!"><img src="' . e($user->avatar_url) . '" class="avatar avatar-md rounded-circle me-2" alt="" /><span class="ms-1 fw-semibold">' . e($user->name) . '</span></a>';
                })
                ->addColumn('roles', function ($user) {
                    return $user->roles->map(function ($role) {
                        $badgeClass = $role->name === 'admin' ? 'bg-danger' : 'bg-primary';
                        return '<span class="badge ' . $badgeClass . '">' . e(ucfirst($role->name)) . '</span>';
                    })->implode(' ');
                })
                ->addColumn('actions', function ($user) {
                    return '<a href="' . route('users.edit', $user->id) . '" class="text-body"><i class="ti ti-edit fs-5"></i></a>
                        <a href="javascript:void(0)" class="link-danger delete-user-btn ms-2" data-id="' . $user->id . '" data-name="' . e($user->name) . '"><i class="ti ti-trash fs-5"></i></a>';
                })
                ->rawColumns(['name', 'roles', 'actions'])
                ->make(true);
        }

        return view('users.index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'max:255', 'unique:users,phone_number'],
            'password' => ['required', 'string', 'min:6'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        $validated['gender'] = 'male';

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);
        $user->assignRole($request->input('roles'));

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['required', 'string', 'max:255', 'unique:users,phone_number,' . $user->id],
            'password' => ['nullable', 'string', 'min:6'],
            'roles' => ['required', 'array'],
            'roles.*' => ['exists:roles,name'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        $user->syncRoles($request->input('roles'));

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 400);
        }

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }
}

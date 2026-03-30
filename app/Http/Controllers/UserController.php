<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->user()->hasPermission('manage_users') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $users = User::with('role')->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermission('manage_users') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasPermission('manage_users') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        if (!auth()->user()->hasPermission('manage_users') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        $roles = Role::where('name', '!=', 'Super Admin')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->user()->hasPermission('manage_users') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // Users cannot change their own role
        if ($request->user()->id === $user->id && $request->role_id !== $user->role_id) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(Request $request, User $user)
    {
        if (!auth()->user()->hasPermission('manage_users') && !auth()->user()->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }
        // Cannot delete own account
        if ($request->user()->id === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Cannot delete Super Admin users (except Super Admin themselves)
        if ($user->isSuperAdmin() && !$request->user()->isSuperAdmin()) {
            return back()->with('error', 'Only Super Admin can delete Super Admin accounts.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || ! method_exists($user, 'hasRole') || ! $user->hasRole('Administrator')) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->pluck('name');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'roles' => 'array',
            'roles.*' => 'string|exists:roles,name',
        ]);

        $roles = $data['roles'] ?? [];

        // Sync roles (replace existing roles)
        $user->syncRoles($roles);

        return redirect()->route('admin.users.index')->with('success', 'Roles updated.');
    }
}

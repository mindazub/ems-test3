<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('roles.index', compact('users'));
    }

    public function edit(User $user)
    {
        return view('roles.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:customer,installer,manager',
        ]);

        $user->role = $request->role;
        $user->save(); // ✅ must be here to persist the change

        return redirect()->route('roles.index')->with('success', 'User role updated successfully.');
    }
}

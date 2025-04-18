<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        // All non-admin customers
        $customers = User::where('role', '!=', 'admin')->latest()->get();
        // $customers = User::where('role', '=', 'customer')->latest()->get();

        return view('customers.index', compact('customers'));
    }

    public function show(User $user)
    {
        // Only allow access to non-admin customers' profiles
        if ($user->role === 'admin') {
            abort(403);
        }

        return view('customers.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($user->role === 'admin') {
            abort(403);
        }

        return view('customers.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'in:customer,manager,installer'],
            'status' => ['nullable', 'in:active,suspended,pending'],
        ]);

        $user->update($validated);

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }
}

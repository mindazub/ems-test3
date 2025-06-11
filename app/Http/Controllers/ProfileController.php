<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Update the user's settings.
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'time_format' => 'required|in:12,24',
            'time_offset' => 'required|integer|min:-23|max:23'
        ]);
        
        $user = $request->user();
        
        // Get current settings or initialize as empty array
        $settings = $user->settings ?? [];
        
        // Make sure we have an array
        if (!is_array($settings)) {
            $settings = [];
        }
        
        // Update time format
        $settings['time_format'] = $request->input('time_format');
        
        // Update time offset (stored as separate column)
        $user->time_offset = $request->input('time_offset');
        
        // Save the updated settings
        $user->settings = $settings;
        $user->save();
        
        return Redirect::route('profile.edit')->with('status', 'settings-updated');
    }
}

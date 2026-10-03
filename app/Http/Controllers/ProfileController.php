<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct() { $this->middleware('auth'); }

    public function edit()
    {
        $user = auth()->user();
        $prefs = $user->preferences ?? new UserPreference();
        return view('dashboard.profile', compact('user','prefs'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'bio'      => 'nullable|string|max:500',
            'avatar'   => 'nullable|image|max:2048',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = ['name' => $request->name, 'bio' => $request->bio];

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars','public');
        }

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update preferences
        $prefData = [
            'preferred_cuisines' => $request->preferred_cuisines ?? [],
            'dietary_flags'      => $request->dietary_flags ?? [],
            'spice_level'        => $request->spice_level ?? 'medium',
            'skill_level'        => $request->skill_level ?? 'easy',
            'calorie_target'     => $request->calorie_target,
            'health_goal'        => $request->health_goal ?? 'maintenance',
            'allergies'          => $request->allergies ?? [],
        ];

        UserPreference::updateOrCreate(['user_id' => $user->id], $prefData);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function updateDarkMode(Request $request)
    {
        UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            ['dark_mode' => $request->boolean('dark_mode')]
        );
        return response()->json(['success' => true]);
    }
}

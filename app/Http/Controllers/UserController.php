<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateMeasurements(Request $request)
    {
        $validated = $request->validate([
            'chest' => 'required|numeric|min:20|max:60',
            'waist' => 'required|numeric|min:20|max:50',
            'hips' => 'required|numeric|min:20|max:60',
            'shoulder' => 'required|numeric|min:10|max:30',
        ]);

        $user = auth()->user();

        if (!$user->user_measurements) {
            return response()->json([
                'success' => false,
                'message' => 'Measurements record does not exist for this user.'
            ], 404);
        }

        $user->user_measurements->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Measurements updated successfully!'
        ]);
    }
}

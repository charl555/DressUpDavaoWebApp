<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMeasurements;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegistrationController extends Controller
{
    public function register(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'color_preference' => ['nullable', 'string'],
                'style_preference_2' => ['nullable', 'string'],
                // Measurements are optional
                'chest' => ['nullable', 'numeric', 'min:20', 'max:60'],
                'waist' => ['nullable', 'numeric', 'min:20', 'max:50'],
                'hips' => ['nullable', 'numeric', 'min:20', 'max:60'],
                'shoulder' => ['nullable', 'numeric', 'min:10', 'max:30'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'User',
                'preferences' => [
                    'color' => $validated['color_preference'] ?? null,
                    'occasion' => $validated['style_preference_2'] ?? null,
                ],
            ]);

            // Create measurements if provided
            if ($validated['chest'] || $validated['waist'] || $validated['hips'] || $validated['shoulder']) {
                UserMeasurements::create([
                    'user_id' => $user->id,
                    'chest' => $validated['chest'] ?? null,
                    'waist' => $validated['waist'] ?? null,
                    'hips' => $validated['hips'] ?? null,
                    'shoulder' => $validated['shoulder'] ?? null,
                ]);
            }

            Auth::login($user);

            // Handle AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Welcome to DressUp Davao!',
                    'redirect' => '/'
                ]);
            }

            return redirect('/')->with('success', 'Welcome to DressUp Davao!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed. Please try again.',
                    'error' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['general' => 'Registration failed. Please try again.'])->withInput();
        }
    }

    public function login(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Handle AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful! Welcome back!',
                    'redirect' => '/'
                ]);
            }

            return redirect()->intended('/')->with('success', 'Welcome back!');
        }

        // Handle failed login
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
                'errors' => ['email' => ['The provided credentials do not match our records.']]
            ], 422);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'You have been logged out.');
    }
}

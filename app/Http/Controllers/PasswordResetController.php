<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    // Step 1: Send code
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'Email not found'], 404);
        }

        $code = random_int(100000, 999999);

        // Cache code for 10 minutes
        Cache::put('password_reset_' . $user->email, $code, now()->addMinutes(10));

        try {
            Mail::raw("Your DressUp Davao password reset code is: {$code}", function ($message) use ($user) {
                $message
                    ->to($user->email)
                    ->subject('Your Password Reset Code - DressUp Davao');
            });
        } catch (\Exception $e) {
            Log::error('Email send failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send email'], 500);
        }

        return response()->json(['message' => 'Code sent']);
    }

    // Step 2: Verify code
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $cachedCode = Cache::get('password_reset_' . $request->email);

        if (!$cachedCode || $cachedCode != $request->code) {
            return response()->json(['error' => 'Invalid or expired code'], 400);
        }

        // Mark verified
        Cache::put('password_reset_verified_' . $request->email, true, now()->addMinutes(10));

        return response()->json(['message' => 'Code verified']);
    }

    // Step 3: Reset password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if (!Cache::get('password_reset_verified_' . $request->email)) {
            return response()->json(['error' => 'Unauthorized request'], 401);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Cleanup
        Cache::forget('password_reset_' . $request->email);
        Cache::forget('password_reset_verified_' . $request->email);

        return response()->json(['message' => 'Password successfully reset']);
    }
}

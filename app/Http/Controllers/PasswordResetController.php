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
    private function checkGmailRateLimit($email)
    {
        $key = 'gmail_rate_limit_' . $email;
        $attempts = Cache::get($key, 0);

        // Gmail limit: Max 500 emails/day, 20/hour per sender
        if ($attempts >= 3) {  // Conservative: 3 attempts per hour
            Log::warning('Gmail rate limit hit for email', [
                'email_hash' => hash('sha256', $email),
                'attempts' => $attempts
            ]);
            return false;
        }

        Cache::put($key, $attempts + 1, now()->addHour());
        return true;
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $key = 'password_reset_rate_limit_' . md5($request->email);
        $attempts = Cache::get($key, 0);
        if ($attempts >= 3) {
            Log::warning('Password reset rate limit hit', [
                'email_hash' => hash('sha256', $request->email),
                'attempts' => $attempts
            ]);
            return response()->json([
                'error' => 'Too many reset attempts. Please try again in an hour.'
            ], 429);
        }
        Cache::put($key, $attempts + 1, now()->addHour());

        // ... (rest of the user lookup and code generation remains the same)
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            Log::info('Password reset request for non-existent email', [
                'email_hash' => hash('sha256', $request->email),
                'ip' => $request->ip()
            ]);
            return response()->json([
                'message' => 'If your email exists, a reset code has been sent.'
            ]);
        }

        $code = random_int(100000, 999999);
        Cache::put('password_reset_' . $user->email, $code, now()->addMinutes(10));

        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'code' => $code,
                'ip' => $request->ip()
            ], function ($message) use ($user) {
                $message
                    ->to($user->email)
                    ->subject('Your Password Reset Code - DressUp Davao');
            });

            Log::info('Password reset code sent via Mailjet', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'message' => 'If your email exists, a reset code has been sent.'
            ]);
        } catch (\Exception $e) {
            Log::error('Mailjet email send failed', [
                'error' => $e->getMessage(),
                'user_email' => $user->email,
                'smtp_user' => config('mail.username'),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'error' => 'Unable to send email. Please try again later or contact support.'
            ], 500);
        }
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $cachedCode = Cache::get('password_reset_' . $request->email);

        if (!$cachedCode || $cachedCode != $request->code) {
            Log::warning('Invalid password reset code attempt', [
                'email_hash' => hash('sha256', $request->email),
                'ip' => $request->ip()
            ]);

            return response()->json(['error' => 'Invalid or expired code'], 400);
        }

        Cache::put('password_reset_verified_' . $request->email, true, now()->addMinutes(10));

        Log::info('Password reset code verified', [
            'email_hash' => hash('sha256', $request->email),
            'ip' => $request->ip()
        ]);

        return response()->json(['message' => 'Code verified']);
    }

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

        Log::info('Password reset successful', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $request->ip()
        ]);

        return response()->json(['message' => 'Password successfully reset']);
    }
}

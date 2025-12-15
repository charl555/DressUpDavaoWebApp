<?php

namespace App\Http\Responses;

use App\Models\ActivityLog;
use Filament\Auth\Http\Responses\Contracts\LogoutResponse as LogoutResponseContract;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogoutResponse implements LogoutResponseContract
{
    public function toResponse($request): Response
    {
        // Get user info before logout
        $user = Auth::user();
        $userId = $user?->id;
        $userEmail = $user?->email;
        $userName = $user?->name;

        // Log activity before logout
        if ($userId) {
            ActivityLog::create([
                'user_id' => $userId,
                'action' => 'logout',
                'model_type' => 'User',
                'model_id' => $userId,
                'description' => "User {$userName} ({$userEmail}) logged out from admin panel",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            \Log::info('User logged out from Filament admin', [
                'user_id' => $userId,
                'email' => $userEmail,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'logged_out_at' => now()->toDateTimeString(),
            ]);
        }

        // Perform logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->to(
            filament()->hasLogin()
                ? filament()->getLoginUrl()
                : filament()->getUrl()
        );
    }
}

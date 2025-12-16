<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Models\LoginBlock;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class LoginSecurityService
{
    // Configuration
    protected const MAX_ATTEMPTS = 3;
    protected const BLOCK_DURATION = 20;
    protected const ATTEMPT_WINDOW = 300;  // 5 minutes
    protected const IP_MAX_ATTEMPTS = 10;
    protected const IP_BLOCK_DURATION = 20;

    private function getAttemptKey(string $email): string
    {
        $key = config('app.key');
        return 'login_attempts:' . hash_hmac('sha256', $email, $key);
    }

    private function getIpAttemptKey(string $ip): string
    {
        $key = config('app.key');
        return 'ip_login_attempts:' . hash_hmac('sha256', $ip, $key);
    }

    private function getBlockKey(string $email): string
    {
        $key = config('app.key');
        return 'login_blocked:' . hash_hmac('sha256', $email, $key);
    }

    private function getIpBlockKey(string $ip): string
    {
        $key = config('app.key');
        return 'ip_login_blocked:' . hash_hmac('sha256', $ip, $key);
    }

    public function isEmailBlocked(string $email): array
    {
        // Check cache first
        $blockCacheKey = $this->getBlockKey($email);
        $blockData = Cache::get($blockCacheKey);

        if ($blockData && isset($blockData['blocked_until'])) {
            $blockedUntil = Carbon::parse($blockData['blocked_until']);
            if ($blockedUntil->isFuture()) {
                $remaining = now()->diffInSeconds($blockedUntil, true);
                return [
                    'blocked' => true,
                    'blocked_until' => $blockedUntil,
                    'remaining_seconds' => $remaining,
                    'message' => 'Too many login attempts. Please try again in '
                        . $this->formatSeconds($remaining),
                    'reason' => 'email'
                ];
            }
        }

        $block = LoginBlock::where('email', $email)
            ->where('blocked_until', '>', now())
            ->latest()
            ->first();

        if ($block) {
            $remaining = now()->diffInSeconds($block->blocked_until, true);
            // Update cache
            Cache::put($blockCacheKey, [
                'blocked_until' => $block->blocked_until,
                'attempts' => $block->attempts
            ], $remaining);

            return [
                'blocked' => true,
                'blocked_until' => $block->blocked_until,
                'remaining_seconds' => $remaining,
                'message' => 'Too many login attempts. Please try again in '
                    . $this->formatSeconds($remaining),
                'reason' => 'email'
            ];
        }

        return ['blocked' => false];
    }

    public function isIpBlocked(string $ip): array
    {
        // Check cache first
        $ipBlockKey = $this->getIpBlockKey($ip);
        $blockData = Cache::get($ipBlockKey);

        if ($blockData && isset($blockData['blocked_until'])) {
            $blockedUntil = Carbon::parse($blockData['blocked_until']);
            if ($blockedUntil->isFuture()) {
                $remaining = now()->diffInSeconds($blockedUntil, true);
                return [
                    'blocked' => true,
                    'blocked_until' => $blockedUntil,
                    'remaining_seconds' => $remaining,
                    'message' => 'Your IP has been temporarily blocked. Please try again in '
                        . $this->formatSeconds($remaining),
                    'reason' => 'ip'
                ];
            }
        }

        $block = LoginBlock::where('ip_address', $ip)
            ->whereNull('email')
            ->where('blocked_until', '>', now())
            ->latest()
            ->first();

        if ($block) {
            $remaining = now()->diffInSeconds($block->blocked_until, true);
            Cache::put($ipBlockKey, [
                'blocked_until' => $block->blocked_until,
                'attempts' => $block->attempts
            ], $remaining);

            return [
                'blocked' => true,
                'blocked_until' => $block->blocked_until,
                'remaining_seconds' => $remaining,
                'message' => 'Your IP has been temporarily blocked. Please try again in '
                    . $this->formatSeconds($remaining),
                'reason' => 'ip'
            ];
        }

        return ['blocked' => false];
    }

    public function recordAttempt(string $email, string $ip, bool $success): void
    {
        // Store in database
        LoginAttempt::create([
            'email' => $email,
            'ip_address' => $ip,
            'attempted_at' => now(),
            'success' => $success
        ]);

        if (!$success) {
            $this->handleFailedAttempt($email, $ip);
        } else {
            $this->clearAttempts($email, $ip);
        }
    }

    protected function handleFailedAttempt(string $email, string $ip): void
    {
        $attemptKey = $this->getAttemptKey($email);
        $ipAttemptKey = $this->getIpAttemptKey($ip);

        $emailAttempts = Cache::get($attemptKey, 0) + 1;
        $ipAttempts = Cache::get($ipAttemptKey, 0) + 1;

        Cache::put($attemptKey, $emailAttempts, self::ATTEMPT_WINDOW);
        Cache::put($ipAttemptKey, $ipAttempts, self::ATTEMPT_WINDOW);

        if ($emailAttempts >= self::MAX_ATTEMPTS) {
            $this->blockEmail($email, $ip, $emailAttempts);
        }

        if ($ipAttempts >= self::IP_MAX_ATTEMPTS) {
            $this->blockIp($ip, $email, $ipAttempts);
        }
    }

    protected function blockEmail(string $email, string $ip, int $attempts): void
    {
        $blockedUntil = now()->addSeconds(self::BLOCK_DURATION);

        LoginBlock::create([
            'email' => $email,
            'ip_address' => $ip,
            'attempts' => $attempts,
            'blocked_until' => $blockedUntil,
            'reason' => 'Max failed attempts exceeded'
        ]);

        Cache::put($this->getBlockKey($email), [
            'blocked_until' => $blockedUntil,
            'attempts' => $attempts
        ], self::BLOCK_DURATION);

        // Clear attempts cache
        Cache::forget($this->getAttemptKey($email));
    }

    protected function blockIp(string $ip, string $email, int $attempts): void
    {
        $blockedUntil = now()->addSeconds(self::IP_BLOCK_DURATION);

        LoginBlock::create([
            'email' => null,
            'ip_address' => $ip,
            'attempts' => $attempts,
            'blocked_until' => $blockedUntil,
            'reason' => 'IP max attempts exceeded'
        ]);

        Cache::put($this->getIpBlockKey($ip), [
            'blocked_until' => $blockedUntil,
            'attempts' => $attempts
        ], self::IP_BLOCK_DURATION);

        Cache::forget($this->getIpAttemptKey($ip));
    }

    public function clearAttempts(string $email, string $ip): void
    {
        // Clear cache
        Cache::forget($this->getAttemptKey($email));
        Cache::forget($this->getIpAttemptKey($ip));
        Cache::forget($this->getBlockKey($email));
        Cache::forget($this->getIpBlockKey($ip));

        LoginBlock::where(function ($query) use ($email, $ip) {
            $query
                ->where('email', $email)
                ->orWhere('ip_address', $ip);
        })->where('blocked_until', '>', now())->delete();
    }

    public function getRemainingAttempts(string $email): int
    {
        $attempts = Cache::get($this->getAttemptKey($email), 0);
        return max(0, self::MAX_ATTEMPTS - $attempts);
    }

    protected function formatSeconds(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }

        $minutes = ceil($seconds / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '');
    }

    public function cleanupOldRecords(int $days = 30): void
    {
        $cutoffDate = now()->subDays($days);

        LoginAttempt::where('created_at', '<', $cutoffDate)->delete();
        LoginBlock::where('created_at', '<', $cutoffDate)->delete();
    }
}

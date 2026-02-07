<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserMeasurements;
use App\Services\LoginSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class RegistrationController extends Controller
{
    protected const MAX_ATTEMPTS = 5;

    protected $loginSecurity;

    public function __construct()
    {
        $this->loginSecurity = new LoginSecurityService();
    }

    public function register(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'gender' => ['required', 'string', 'in:Male,Female'],
                'phone_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/', 'unique:users,phone_number'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'color_preference' => ['nullable', 'string'],
                'occasion_preference' => ['nullable', 'string'],
                'fabric_preference' => ['nullable', 'string'],
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
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        }

        try {
            // Create user record
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'gender' => $validated['gender'],
                'phone_number' => $validated['phone_number'],
                'password' => Hash::make($validated['password']),
                'role' => 'User',
                'preferences' => [
                    'color' => $validated['color_preference'] ?? null,
                    'occasion' => $validated['occasion_preference'] ?? null,
                    'fabric' => $validated['fabric_preference'] ?? null,
                ],
            ]);

            // Always create a UserMeasurements record, even if fields are empty
            try {
                $measurementData = [
                    'user_id' => $user->id,
                    'chest' => $validated['chest'] ?? null,
                    'waist' => $validated['waist'] ?? null,
                    'hips' => $validated['hips'] ?? null,
                    'shoulder' => $validated['shoulder'] ?? null,
                ];

                // Log the measurement data for debugging
                \Log::info('Creating UserMeasurements with data:', $measurementData);

                $userMeasurement = UserMeasurements::create($measurementData);

                \Log::info('UserMeasurements created successfully:', ['id' => $userMeasurement->user_measurements_id]);
            } catch (\Exception $measurementException) {
                \Log::error('Failed to create UserMeasurements:', [
                    'error' => $measurementException->getMessage(),
                    'user_id' => $user->id,
                    'measurement_data' => $measurementData ?? null
                ]);
                // Don't fail the entire registration if measurements fail
                // The user can add measurements later
            }

            Auth::login($user);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Registration successful! Welcome to DressUp Davao!',
                    'redirect' => '/',
                ]);
            }

            return redirect('/')->with('success', 'Registration successful! Welcome to DressUp Davao!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration failed. Please try again.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withErrors(['general' => 'Registration failed. Please try again.'])
                ->withInput();
        }
    }

    public function login(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        // Check if request is from mobile app
        $isMobileApp = $request->has('app') || $request->has('mobile_nav');

        // 🔥 PWA FIX: Check if request is from PWA
        $isPwaRequest = $request->header('Sec-Fetch-Dest') === 'empty' ||
            $request->header('X-Requested-With') === 'XMLHttpRequest' ||
            $request->header('X-PWA-Request') === 'true' ||
            !$request->expectsJson();  // PWA often doesn't set proper headers

        // Combine both checks
        $isMobileContext = $isMobileApp || $isPwaRequest;

        // 🔥 PWA FIX: If mobile context request, adjust session handling
        if ($isMobileContext) {
            // Ensure session is properly started
            $request->session()->start();

            // Re-generate session token for mobile context
            $request->session()->regenerateToken();

            // Log mobile request for debugging
            \Log::info('Mobile Login Attempt', [
                'ip' => $request->ip(),
                'is_mobile_app' => $isMobileApp,
                'is_pwa' => $isPwaRequest,
                'headers' => $request->headers->all(),
                'session_id' => $request->session()->getId(),
            ]);
        }

        try {
            // Different validation rules for mobile app vs web
            $validationRules = [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ];

            // Only require Turnstile for web (not mobile app)
            if (!$isMobileApp) {
                $validationRules['cf-turnstile-response'] = ['required', 'string'];
            }

            $credentials = $request->validate($validationRules);

            // Check if email is blocked
            $emailBlock = $this->loginSecurity->isEmailBlocked($credentials['email']);
            if ($emailBlock['blocked']) {
                return $this->handleBlockedLogin($request, $emailBlock, $isMobileContext);
            }

            // Check if IP is blocked
            $ipBlock = $this->loginSecurity->isIpBlocked($request->ip());
            if ($ipBlock['blocked']) {
                return $this->handleBlockedLogin($request, $ipBlock, $isMobileContext);
            }

            // Validate Turnstile token only for web (not mobile app)
            if (!$isMobileApp) {
                $turnstileValid = $this->validateTurnstile($credentials['cf-turnstile-response']);
                if (!$turnstileValid['success']) {
                    $this->loginSecurity->recordAttempt($credentials['email'], $request->ip(), false);

                    $responseData = [
                        'success' => false,
                        'message' => $turnstileValid['message'],
                        'errors' => ['cf-turnstile-response' => [$turnstileValid['message']]]
                    ];

                    // 🔥 Mobile Context FIX: Add CSRF token for mobile
                    if ($isMobileContext) {
                        $responseData['csrf_token'] = csrf_token();
                    }

                    return response()->json($responseData, 422);
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $responseData = [
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ];

            // 🔥 Mobile Context FIX: Add CSRF token for mobile
            if ($isMobileContext) {
                $responseData['csrf_token'] = csrf_token();
            }

            if ($request->expectsJson() || $isMobileContext) {
                return response()->json($responseData, 422);
            }
            throw $e;
        }

        // Check if user exists and is an admin
        $user = User::where('email', $credentials['email'])->first();

        if ($user && in_array($user->role, ['Admin', 'SuperAdmin'])) {
            $this->loginSecurity->recordAttempt($credentials['email'], $request->ip(), false);

            $responseData = [
                'success' => false,
                'message' => 'Admin accounts must use the admin login page.',
                'errors' => ['email' => ['Admin accounts must use the admin login page.']]
            ];

            // 🔥 Mobile Context FIX: Add CSRF token for mobile
            if ($isMobileContext) {
                $responseData['csrf_token'] = csrf_token();
            }

            if ($request->expectsJson() || $isMobileContext) {
                return response()->json($responseData, 422);
            }

            return back()->withErrors([
                'email' => 'Admin accounts must use the admin login page.',
            ])->onlyInput('email');
        }

        if (Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            // Record successful attempt
            $this->loginSecurity->recordAttempt($credentials['email'], $request->ip(), true);

            // Log activity
            \App\Models\ActivityLog::log(
                'login',
                "User {$credentials['email']} logged in successfully",
                'User',
                Auth::id()
            );

            $request->session()->regenerate();

            // 🔥 Mobile Context FIX: Special handling for mobile requests
            if ($isMobileContext) {
                // For mobile, ensure session is properly saved
                $request->session()->save();

                // Return JSON with session info for mobile
                $redirectUrl = $isMobileApp ? '/?app=1&mobile_nav=true' : '/';

                return response()->json([
                    'success' => true,
                    'message' => 'Login successful! Welcome back!',
                    'redirect' => $redirectUrl,
                    'session_refreshed' => true,
                    'csrf_token' => csrf_token(),  // Send new CSRF token
                    'is_mobile' => $isMobileApp,
                    'is_pwa' => $isPwaRequest,
                ]);
            }

            // Handle regular AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login successful! Welcome back!',
                    'redirect' => '/'
                ]);
            }

            return redirect()->intended('/')->with('success', 'Login successful! Welcome back!');
        }

        // Handle failed login
        $this->loginSecurity->recordAttempt($credentials['email'], $request->ip(), false);
        $remainingAttempts = $this->loginSecurity->getRemainingAttempts($credentials['email']);

        $message = 'The provided credentials do not match our records.';
        if ($remainingAttempts > 0 && $remainingAttempts < self::MAX_ATTEMPTS) {
            $message .= " You have {$remainingAttempts} attempt(s) remaining.";
        }

        $responseData = [
            'success' => false,
            'message' => $message,
            'remaining_attempts' => $remainingAttempts,
            'errors' => ['email' => [$message]]
        ];

        // 🔥 Mobile Context FIX: Add CSRF token for mobile
        if ($isMobileContext) {
            $responseData['csrf_token'] = csrf_token();
        }

        if ($request->expectsJson() || $isMobileContext) {
            return response()->json($responseData, 422);
        }

        return back()->withErrors([
            'email' => $message,
        ])->onlyInput('email');
    }

    /**
     * Handle blocked login attempts
     */
    protected function handleBlockedLogin(Request $request, array $blockData, bool $isMobileContext = false)
    {
        $message = $blockData['message'];

        $responseData = [
            'success' => false,
            'message' => $message,
            'blocked' => true,
            'blocked_until' => $blockData['blocked_until']->toIso8601String(),
            'remaining_seconds' => $blockData['remaining_seconds'],
            'errors' => ['email' => [$message]]
        ];

        // 🔥 Mobile Context FIX: Add CSRF token for mobile
        if ($isMobileContext) {
            $responseData['csrf_token'] = csrf_token();
        }

        if ($request->expectsJson() || $isMobileContext) {
            return response()->json($responseData, 429);  // 429 Too Many Requests
        }

        return back()
            ->withErrors(['email' => $message])
            ->withInput($request->only('email', 'remember'))
            ->with('blocked', true)
            ->with('blocked_until', $blockData['blocked_until']->toIso8601String())
            ->with('remaining_seconds', $blockData['remaining_seconds']);
    }

    /**
     * Validate Cloudflare Turnstile token
     */
    private function validateTurnstile(string $token): array
    {
        $secretKey = config('services.cloudflare.secret_key');

        if (empty($secretKey)) {
            \Log::error('Cloudflare Turnstile secret key is not configured');
            return [
                'success' => false,
                'message' => 'Security configuration error. Please contact support.'
            ];
        }

        if (empty($token)) {
            return [
                'success' => false,
                'message' => 'Please complete the security check.'
            ];
        }

        try {
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => request()->ip(),
            ]);

            $data = $response->json();

            if (!$data['success']) {
                \Log::warning('Turnstile verification failed', [
                    'error_codes' => $data['error-codes'] ?? [],
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);

                $errorMessage = $this->getTurnstileErrorMessage($data['error-codes'] ?? []);

                return [
                    'success' => false,
                    'message' => $errorMessage
                ];
            }

            // UPDATED: Check hostname against ALLOWED domains
            $allowedDomains = ['dressupdavao.shop', 'localhost', '127.0.0.1'];

            if (isset($data['hostname']) && !in_array($data['hostname'], $allowedDomains)) {
                \Log::warning('Turnstile hostname mismatch', [
                    'allowed' => $allowedDomains,
                    'received' => $data['hostname'],
                    'ip' => request()->ip(),
                ]);

                return [
                    'success' => false,
                    'message' => 'Security verification failed. Invalid domain.'
                ];
            }

            return [
                'success' => true,
                'message' => 'Verification successful'
            ];
        } catch (\Exception $e) {
            \Log::error('Turnstile verification error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Unable to verify security check. Please try again.'
            ];
        }
    }

    /**
     * Validate Turnstile token via AJAX (for client-side validation if needed)
     */
    public function validateTurnstileAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $result = $this->validateTurnstile($request->token);

        return response()->json($result);
    }

    /**
     * Get user-friendly error messages for Turnstile errors
     */
    private function getTurnstileErrorMessage(array $errorCodes): string
    {
        $errors = [
            'missing-input-secret' => 'Security configuration error.',
            'invalid-input-secret' => 'Security configuration error.',
            'missing-input-response' => 'Please complete the security check.',
            'invalid-input-response' => 'Security verification expired or invalid. Please try again.',
            'bad-request' => 'Invalid request.',
            'timeout-or-duplicate' => 'Security check expired. Please try again.',
            'internal-error' => 'Security service error. Please try again.',
        ];

        foreach ($errorCodes as $code) {
            if (isset($errors[$code])) {
                return $errors[$code];
            }
        }

        return 'Security verification failed. Please try again.';
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $userId = $user?->id;
        $userEmail = $user?->email;
        $userName = $user?->name;

        // Log activity before logout
        if ($userId) {
            \App\Models\ActivityLog::log(
                'logout',
                "User {$userName} ({$userEmail}) logged out",
                'User',
                $userId
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        \Log::info('User logged out', [
            'user_id' => $userId,
            'email' => $userEmail,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'logged_out_at' => now()->toDateTimeString(),
        ]);

        // Check if it's a mobile app request
        $isMobileApp = $request->has('app') || $request->has('mobile_nav');

        if ($isMobileApp) {
            return redirect('/?app=1&mobile_nav=true')->with('success', 'You have been logged out.');
        }

        return redirect('/')->with('success', 'You have been logged out.');
    }

    public function checkEmail(Request $request)
    {
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
}

@php
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
    
    // Get session data for mobile
    $blockedUntil = session('blocked_until');
    $remainingAttempts = session('remaining_attempts');
    $errors = session('errors') ?: new \Illuminate\Support\MessageBag();
    $generalError = session('error') ?? $errors->first('email') ?? '';
@endphp

<div class="min-h-screen bg-white flex flex-col" x-data="mobileLogin()">
    <!-- Header with Back Button -->
    <div class="sticky top-0 z-50 bg-white border-b border-gray-200 px-4 py-3">
        <div class="flex items-center justify-between">
            <a href="{{ $isMobileApp ? '/?app=1&mobile_nav=true' : '/' }}" 
               class="flex items-center text-gray-600 active:scale-95 transition-transform duration-200">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span class="text-sm font-medium">Back</span>
            </a>
            
            <a href="/">
                <img class="h-10 w-auto" src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao" />
            </a>
            
            <div class="w-12"></div> <!-- Spacer for balance -->
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 px-6 py-8 overflow-y-auto no-scrollbar">
        <div class="max-w-md mx-auto">
            <!-- Welcome Section -->
            <div class="text-center mb-10">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome Back</h1>
                <p class="text-gray-600">Sign in to continue to your account</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" 
                  class="space-y-6 bg-white p-6 rounded-2xl shadow-sm border border-gray-100"
                  @submit.prevent="submitForm">
                @csrf
                
                <!-- Hidden Turnstile field for mobile (bypass) -->
                <input type="hidden" name="cf-turnstile-response" value="mobile-bypass-token">
                
                @if($isMobileApp)
                    <!-- Mobile identifiers -->
                    <input type="hidden" name="app" value="1">
                    <input type="hidden" name="mobile_nav" value="true">
                @endif

                <!-- Email Field -->
                <div>
                    <label for="mobile-email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="email" 
                               name="email" 
                               id="mobile-email" 
                               x-model="form.email"
                               :class="{ 'border-red-300': errors.email }"
                               value="{{ old('email') }}"
                               autocomplete="email" 
                               required 
                               class="block w-full pl-10 pr-3 py-4 border border-gray-300 rounded-xl bg-gray-50
                                      focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                      placeholder-gray-500 text-gray-900 text-base-mobile
                                      transition-all duration-200"
                               placeholder="you@example.com"
                               autofocus>
                    </div>
                    <p x-show="errors.email" x-text="errors.email" class="mt-2 text-sm text-red-600"></p>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="mobile-password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               id="mobile-password" 
                               x-model="form.password"
                               :class="{ 'border-red-300': errors.password }"
                               autocomplete="current-password" 
                               required 
                               class="block w-full pl-10 pr-12 py-4 border border-gray-300 rounded-xl bg-gray-50
                                      focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                      placeholder-gray-500 text-gray-900 text-base-mobile
                                      transition-all duration-200"
                               placeholder="Enter your password">
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                    <p x-show="errors.password" x-text="errors.password" class="mt-2 text-sm text-red-600"></p>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input type="checkbox" 
                               name="remember" 
                               id="remember-me-mobile" 
                               x-model="form.remember"
                               class="h-5 w-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                               {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember-me-mobile" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <a href="/forgot-password{{ $isMobileApp ? '?app=1&mobile_nav=true' : '' }}"
                       class="text-sm font-medium text-purple-600 hover:text-purple-500 transition-colors duration-200">
                        Forgot password?
                    </a>
                </div>

                <!-- Error Messages -->
                <div x-show="generalError" class="rounded-xl bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700" x-text="generalError"></p>
                        </div>
                    </div>
                </div>

                <!-- Login Attempts Warning -->
                <div x-show="remainingAttempts && remainingAttempts > 0 && remainingAttempts <= 3 && !blockedUntil" 
                     class="rounded-xl bg-orange-50 p-4 border border-orange-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-orange-800">Security Notice</h3>
                            <div class="mt-1 text-sm text-orange-700">
                                <p>You have <span class="font-semibold" x-text="remainingAttempts"></span> attempt(s) remaining.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Blocked Warning -->
                <div x-show="blockedUntil" class="rounded-xl bg-yellow-50 p-4 border border-yellow-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Login Temporarily Blocked</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Too many login attempts. Please try again later.</p>
                                <div class="mt-2" x-show="blockedTimeRemaining">
                                    <p class="font-medium">Time remaining: <span x-text="blockedTimeRemaining"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            :disabled="isLoading || blockedUntil"
                            class="w-full py-4 px-6 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl
                                   hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2
                                   transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed
                                   flex items-center justify-center">
                        <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-show="!isLoading">Sign In</span>
                        <span x-show="isLoading">Signing In...</span>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">Don't have an account?</span>
                </div>
            </div>

            <!-- Register Link -->
            <a href="/register{{ $isMobileApp ? '?app=1&mobile_nav=true' : '' }}"
               class="block w-full py-4 px-6 border-2 border-purple-600 text-purple-600 font-semibold rounded-xl
                      hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2
                      transition-all duration-200 active:scale-95 text-center">
                Create New Account
            </a>
        </div>
    </div>

    <!-- Security Notice (Mobile-only) -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
        <div class="flex items-center text-gray-600 text-sm">
            <svg class="w-5 h-5 mr-2 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
            </svg>
            <span>Your login information is secured and encrypted.</span>
        </div>
    </div>

    <!-- Bottom Navigation Spacer (for mobile app) -->
    @if($isMobileApp)
        <div class="h-16"></div>
    @endif
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mobileLogin', () => ({
            form: {
                email: '{{ old("email") }}',
                password: '',
                remember: {{ old("remember") ? 'true' : 'false' }}
            },
            errors: {
                email: '',
                password: ''
            },
            generalError: '{{ $generalError }}',
            showPassword: false,
            isLoading: false,
            blockedUntil: @json($blockedUntil),
            remainingAttempts: @json($remainingAttempts),
            blockedTimeRemaining: '',
            blockedTimer: null,

            init() {
                // Check for blocked timer
                if (this.blockedUntil) {
                    this.startBlockTimer();
                }
                
                // Add touch feedback
                this.addTouchFeedback();
            },

            startBlockTimer() {
                const blockedUntil = new Date(this.blockedUntil);
                const updateTimer = () => {
                    const now = new Date();
                    const remaining = blockedUntil - now;
                    
                    if (remaining <= 0) {
                        this.blockedUntil = null;
                        if (this.blockedTimer) {
                            clearInterval(this.blockedTimer);
                        }
                        return;
                    }
                    
                    const minutes = Math.floor(remaining / (1000 * 60));
                    const seconds = Math.floor((remaining % (1000 * 60)) / 1000);
                    this.blockedTimeRemaining = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                };
                
                updateTimer();
                this.blockedTimer = setInterval(updateTimer, 1000);
            },

            addTouchFeedback() {
                // Add active state to interactive elements
                document.querySelectorAll('a, button').forEach(element => {
                    element.addEventListener('touchstart', () => {
                        element.classList.add('active:scale-95');
                    });
                    
                    element.addEventListener('touchend', () => {
                        element.classList.remove('active:scale-95');
                    });
                });
            },

            validateForm() {
                this.errors.email = '';
                this.errors.password = '';
                this.generalError = '';
                
                let isValid = true;
                
                // Email validation
                if (!this.form.email) {
                    this.errors.email = 'Email is required';
                    isValid = false;
                } else if (!this.isValidEmail(this.form.email)) {
                    this.errors.email = 'Please enter a valid email address';
                    isValid = false;
                }
                
                // Password validation
                if (!this.form.password) {
                    this.errors.password = 'Password is required';
                    isValid = false;
                }
                
                return isValid;
            },
            
            isValidEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            },

            async submitForm() {
                if (this.blockedUntil) {
                    if (typeof showToast === 'function') {
                        showToast('Login is temporarily blocked. Please try again later.', 'warning');
                    }
                    return;
                }
                
                if (!this.validateForm()) {
                    return;
                }
                
                this.isLoading = true;
                
                try {
                    const formData = new FormData();
                    formData.append('email', this.form.email);
                    formData.append('password', this.form.password);
                    formData.append('cf-turnstile-response', 'mobile-bypass-token');
                    if (this.form.remember) {
                        formData.append('remember', '1');
                    }
                    
                    // Add mobile identifiers
                    @if($isMobileApp)
                        formData.append('app', '1');
                        formData.append('mobile_nav', 'true');
                    @endif
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    formData.append('_token', csrfToken);
                    
                    const response = await fetch('{{ route("login") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include'
                    });
                    
                    const data = await response.json();
                    
                    if (response.ok && data.success) {
                        // Success - redirect
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Login successful!', 'success');
                            setTimeout(() => {
                                window.location.href = data.redirect || '/';
                            }, 1000);
                        } else {
                            window.location.href = data.redirect || '/';
                        }
                    } else {
                        // Handle errors
                        if (data.blocked) {
                            this.blockedUntil = data.blocked_until;
                            this.startBlockTimer();
                            this.generalError = data.message;
                        } else if (data.remaining_attempts !== undefined) {
                            this.remainingAttempts = data.remaining_attempts;
                            this.generalError = data.message;
                        } else if (data.errors) {
                            if (data.errors.email) {
                                this.errors.email = data.errors.email[0];
                            }
                            if (data.errors.password) {
                                this.errors.password = data.errors.password[0];
                            }
                        } else {
                            this.generalError = data.message || 'Invalid credentials';
                        }
                        
                        if (typeof showToast === 'function') {
                            showToast(this.generalError, 'error');
                        }
                    }
                } catch (error) {
                    console.error('Login error:', error);
                    this.generalError = 'An error occurred. Please try again.';
                    if (typeof showToast === 'function') {
                        showToast(this.generalError, 'error');
                    }
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    });
</script>
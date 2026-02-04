<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8" x-data="loginForm()">
    <div class="w-full max-w-md space-y-8">
        <!-- Header -->
        <div class="text-center">
            <a href="/">
                <img class="mx-auto h-18 w-auto" src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao" />
                <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Sign in to your account</h2>
                <p class="mt-2 text-sm text-gray-600">Welcome back! Please enter your details.</p>
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg border border-gray-200">
            <form @submit.prevent="submitForm" class="space-y-6">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="email" name="email" id="email" x-model="form.email" @blur="validateField('email')"
                            autocomplete="email" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                   transition-colors duration-200"
                            :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.email }"
                            placeholder="Enter your email" :disabled="loading || isBlocked" />
                    </div>
                    <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                            x-model="form.password" @blur="validateField('password')" autocomplete="current-password"
                            required class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                   focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                   transition-colors duration-200"
                            :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.password }"
                            placeholder="Enter your password" :disabled="loading || isBlocked" />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600
                                   focus:outline-none focus:text-gray-600 transition-colors duration-200"
                            :disabled="loading || isBlocked">
                            <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showPassword" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                    <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-red-600"></p>
                </div>

                <!-- Login Attempts Warning -->
                <div x-show="blockedUntil" x-cloak class="rounded-md bg-yellow-50 p-4 border border-yellow-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Login Temporarily Blocked</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p x-text="blockedMessage"></p>
                                <div class="mt-2">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-yellow-200 rounded-full h-2">
                                            <div class="bg-yellow-600 h-2 rounded-full transition-all duration-1000"
                                                :style="{ width: (blockedProgress) + '%' }"></div>
                                        </div>
                                        <span class="text-xs font-medium text-yellow-800"
                                            x-text="blockedTimeRemaining"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Remaining Attempts Warning -->
                <div x-show="remainingAttempts !== null && remainingAttempts > 0 && remainingAttempts <= 3 && !blockedUntil"
                    x-cloak class="rounded-md bg-orange-50 p-4 border border-orange-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-orange-800">Security Warning</h3>
                            <div class="mt-1 text-sm text-orange-700">
                                <p>You have <span class="font-semibold" x-text="remainingAttempts"></span>
                                    attempt(s) remaining before temporary lockout.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cloudflare Turnstile Widget -->
                <div>
                    <div id="cf-turnstile-widget" class="flex justify-center"></div>
                    <input type="hidden" name="cf-turnstile-response" id="cf-turnstile-response">
                    <p x-show="errors.turnstile" x-text="errors.turnstile"
                        class="mt-1 text-sm text-red-600 text-center"></p>
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" x-model="form.remember"
                            class="h-4 w-4 text-violet-600 focus:ring-violet-500 border-gray-300 rounded"
                            :disabled="loading || isBlocked">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="/forgot-password"
                            class="font-medium text-violet-600 hover:text-violet-500 transition-colors duration-200">
                            Forgot your password?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" :disabled="loading || !turnstileToken || isBlocked"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white
                               bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                               disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow-md" :class="{ 
                            'bg-violet-400 hover:bg-violet-400': !turnstileToken,
                            'bg-yellow-600 hover:bg-yellow-700': isBlocked
                        }">
                        <template x-if="isBlocked">
                            <span>Login Blocked</span>
                        </template>
                        <template x-if="!isBlocked">
                            <span x-show="!loading">Sign in</span>
                        </template>
                        <span x-show="loading && !isBlocked" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Signing in...
                        </span>
                    </button>
                </div>

                <!-- Error Messages -->
                <div x-show="generalError" class="rounded-md bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Authentication failed</h3>
                            <p class="mt-1 text-sm text-red-700" x-text="generalError"></p>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Register Link -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Don't have an account?
                <a href="/register"
                    class="font-medium text-violet-600 hover:text-violet-500 transition-colors duration-200">
                    Create an account
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Cloudflare Turnstile Script -->
<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

<script>
    function loginForm() {
        return {
            form: {
                email: '',
                password: '',
                remember: false
            },
            errors: {},
            generalError: '',
            showPassword: false,
            loading: false,
            turnstileToken: null,
            turnstileWidgetId: null,
            blockedUntil: null,
            blockedTimer: null,
            blockedProgress: 0,
            blockedTimeRemaining: '',
            blockedMessage: '',
            remainingAttempts: null,
            isBlocked: false,

            init() {
                // Check for session storage data on page load
                this.checkSessionStorage();

                // Initialize Turnstile after Alpine is loaded
                this.$nextTick(() => {
                    this.initializeTurnstile();
                });
            },

            checkSessionStorage() {
                // Check if there are any session-blocked messages
                const blockedUntilISO = sessionStorage.getItem('login_blocked_until');
                if (blockedUntilISO) {
                    this.startBlockTimer(blockedUntilISO);
                }

                // Check for remaining attempts in session
                const attempts = sessionStorage.getItem('remaining_attempts');
                if (attempts) {
                    this.remainingAttempts = parseInt(attempts);
                }

                // Check for session flash data (for non-AJAX submissions)
                this.checkFlashData();
            },

            checkFlashData() {
                // Check if there's any flash data from Laravel session
                const blockedFlash = document.querySelector('[data-blocked-until]');
                if (blockedFlash) {
                    const blockedUntilISO = blockedFlash.getAttribute('data-blocked-until');
                    this.startBlockTimer(blockedUntilISO);
                }
            },

            startBlockTimer(blockedUntilISO) {
                const blockedUntil = new Date(blockedUntilISO);
                const now = new Date();
                const totalSeconds = Math.floor((blockedUntil - now) / 1000);

                if (totalSeconds <= 0) {
                    this.clearBlock();
                    return;
                }

                this.blockedUntil = blockedUntilISO;
                this.isBlocked = true;
                this.updateBlockTimer();

                // Start timer
                this.blockedTimer = setInterval(() => {
                    this.updateBlockTimer();
                }, 1000);

                // Store in sessionStorage for persistence
                sessionStorage.setItem('login_blocked_until', blockedUntilISO);
            },

            updateBlockTimer() {
                if (!this.blockedUntil) {
                    this.clearBlock();
                    return;
                }

                const blockedUntil = new Date(this.blockedUntil);
                const now = new Date();
                const remainingSeconds = Math.floor((blockedUntil - now) / 1000);

                if (remainingSeconds <= 0) {
                    this.clearBlock();
                    if (typeof showToast === 'function') {
                        showToast('You can now try to log in again.', 'info');
                    }
                    return;
                }

                // Update progress (15 minutes = 900 seconds)
                const totalSeconds = 900;
                this.blockedProgress = ((totalSeconds - remainingSeconds) / totalSeconds) * 100;

                // Format time
                const minutes = Math.floor(remainingSeconds / 60);
                const seconds = remainingSeconds % 60;
                this.blockedTimeRemaining = `${minutes}:${seconds.toString().padStart(2, '0')}`;

                this.blockedMessage = `Too many login attempts. Please try again in ${minutes} minute${minutes !== 1 ? 's' : ''}.`;
            },

            clearBlock() {
                if (this.blockedTimer) {
                    clearInterval(this.blockedTimer);
                    this.blockedTimer = null;
                }
                this.blockedUntil = null;
                this.blockedProgress = 0;
                this.blockedTimeRemaining = '';
                this.blockedMessage = '';
                this.isBlocked = false;
                sessionStorage.removeItem('login_blocked_until');

                // Enable form fields
                this.enableForm();
            },

            enableForm() {
                // Form fields will be automatically enabled when isBlocked becomes false
                // due to the :disabled bindings
            },

            initializeTurnstile() {
                // Wait for turnstile to be available
                const checkAndRender = () => {
                    if (typeof turnstile === 'undefined') {
                        // Check again in 50ms
                        setTimeout(checkAndRender, 50);
                        return;
                    }

                    // Render widget now that turnstile is available
                    this.turnstileWidgetId = turnstile.render('#cf-turnstile-widget', {
                        sitekey: '{{ config("services.cloudflare.site_key") }}',
                        theme: 'auto',
                        size: 'normal',
                        callback: (token) => {
                            this.turnstileToken = token;
                            document.getElementById('cf-turnstile-response').value = token;
                            this.errors.turnstile = '';
                        },
                        'expired-callback': () => {
                            this.turnstileToken = null;
                            document.getElementById('cf-turnstile-response').value = '';
                            this.errors.turnstile = 'Security check expired.';
                        },
                        'error-callback': () => {
                            this.turnstileToken = null;
                            document.getElementById('cf-turnstile-response').value = '';
                            this.errors.turnstile = 'Security verification failed.';
                        }
                    });
                };

                // Start the check
                checkAndRender();
            },

            resetTurnstile() {
                if (this.turnstileWidgetId && typeof turnstile !== 'undefined') {
                    turnstile.reset(this.turnstileWidgetId);
                    this.turnstileToken = null;
                    document.getElementById('cf-turnstile-response').value = '';
                    this.errors.turnstile = '';
                }
            },

            validateField(field) {
                this.errors[field] = '';

                switch (field) {
                    case 'email':
                        if (!this.form.email) {
                            this.errors.email = 'Email is required';
                        } else if (!this.isValidEmail(this.form.email)) {
                            this.errors.email = 'Please enter a valid email address';
                        } else if (this.isBlocked) {
                            this.errors.email = this.blockedMessage;
                        }
                        break;
                    case 'password':
                        if (!this.form.password) {
                            this.errors.password = 'Password is required';
                        } else if (this.form.password.length < 6) {
                            this.errors.password = 'Password must be at least 6 characters';
                        }
                        break;
                }
            },

            isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },

            validateForm() {
                // Don't validate if blocked
                if (this.isBlocked) {
                    return false;
                }

                this.validateField('email');
                this.validateField('password');

                // Validate Turnstile
                if (!this.turnstileToken) {
                    this.errors.turnstile = 'Please complete the security check';
                    return false;
                }

                return !this.errors.email && !this.errors.password && !this.errors.turnstile;
            },

            async submitForm() {
                // Don't submit if blocked
                if (this.isBlocked) {
                    if (typeof showToast === 'function') {
                        showToast(this.blockedMessage, 'warning');
                    }
                    return;
                }

                this.generalError = '';
                this.errors.turnstile = '';

                if (!this.validateForm()) {
                    return;
                }

                this.loading = true;

                try {
                    const formData = new FormData();
                    formData.append('email', this.form.email);
                    formData.append('password', this.form.password);
                    formData.append('cf-turnstile-response', this.turnstileToken);
                    if (this.form.remember) {
                        formData.append('remember', '1');
                    }

                    // 🔥 PWA FIX: Get CSRF token properly
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    formData.append('_token', csrfToken);

                    // 🔥 PWA FIX: Add PWA detection headers
                    const headers = {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    };

                    // Check if running in PWA mode
                    const isPWA = window.matchMedia('(display-mode: standalone)').matches ||
                        window.navigator.standalone;

                    if (isPWA) {
                        headers['X-PWA-Request'] = 'true';
                        headers['Sec-Fetch-Dest'] = 'empty';
                        console.log('PWA Login: Adding PWA-specific headers');
                    }

                    const response = await fetch('{{ route("login") }}', {
                        method: 'POST',
                        body: formData,
                        headers: headers,
                        credentials: 'include' // 🔥 IMPORTANT: Include cookies for PWA
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        // Clear any block data
                        this.clearBlock();
                        sessionStorage.removeItem('remaining_attempts');

                        // 🔥 PWA FIX: Update CSRF token if provided
                        if (data.csrf_token) {
                            const metaTag = document.querySelector('meta[name="csrf-token"]');
                            if (metaTag) {
                                metaTag.setAttribute('content', data.csrf_token);
                                console.log('PWA Login: Updated CSRF token');
                            }
                        }

                        // Show success toast before redirect
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Login successful! Welcome back!', 'success');
                            // Delay redirect to show toast
                            setTimeout(() => {
                                window.location.href = data.redirect || '/';
                            }, 1000);
                        } else {
                            window.location.href = data.redirect || '/';
                        }
                    } else {
                        // Reset Turnstile on failed attempt
                        this.resetTurnstile();

                        // 🔥 PWA FIX: Update CSRF token if provided (even on error)
                        if (data.csrf_token) {
                            const metaTag = document.querySelector('meta[name="csrf-token"]');
                            if (metaTag) {
                                metaTag.setAttribute('content', data.csrf_token);
                                console.log('PWA Login: Updated CSRF token on error');
                            }
                        }

                        // Handle blocked response
                        if (data.blocked) {
                            this.startBlockTimer(data.blocked_until);
                            this.blockedMessage = data.message;
                            sessionStorage.setItem('remaining_attempts', 0);

                            // Show warning toast
                            if (typeof showToast === 'function') {
                                showToast(this.blockedMessage, 'warning');
                            }
                        } else if (data.remaining_attempts !== undefined) {
                            this.remainingAttempts = data.remaining_attempts;
                            sessionStorage.setItem('remaining_attempts', data.remaining_attempts);

                            // Show warning if low attempts
                            if (data.remaining_attempts <= 3) {
                                if (typeof showToast === 'function') {
                                    showToast(`Warning: Only ${data.remaining_attempts} attempt(s) remaining before temporary block.`, 'warning');
                                }
                            }
                        }

                        if (data.errors) {
                            this.errors = data.errors;
                            // Show specific Turnstile error if present
                            if (data.errors['cf-turnstile-response']) {
                                this.errors.turnstile = data.errors['cf-turnstile-response'][0];
                            }
                        } else {
                            this.generalError = data.message || 'Invalid credentials. Please try again.';
                            // Show error toast
                            if (typeof showToast === 'function') {
                                showToast(this.generalError, 'error');
                            }
                        }
                    }
                } catch (error) {
                    this.resetTurnstile();
                    this.generalError = 'An error occurred. Please try again.';
                    console.error('Login error:', error);
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>


<div id="flash-data" data-blocked-until="{{ session('blocked_until') }}" style="display: none;">
</div>
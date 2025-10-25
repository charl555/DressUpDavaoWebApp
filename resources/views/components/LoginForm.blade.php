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
                            placeholder="Enter your email" />
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
                            placeholder="Enter your password" />
                        <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600
                                   focus:outline-none focus:text-gray-600 transition-colors duration-200">
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

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember" type="checkbox" x-model="form.remember"
                            class="h-4 w-4 text-violet-600 focus:ring-violet-500 border-gray-300 rounded">
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
                    <button type="submit" :disabled="loading"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white
                               bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                               disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow-md">
                        <span x-show="!loading">Sign in</span>
                        <span x-show="loading" class="flex items-center">
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

            validateField(field) {
                this.errors[field] = '';

                switch (field) {
                    case 'email':
                        if (!this.form.email) {
                            this.errors.email = 'Email is required';
                        } else if (!this.isValidEmail(this.form.email)) {
                            this.errors.email = 'Please enter a valid email address';
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
                this.validateField('email');
                this.validateField('password');
                return !this.errors.email && !this.errors.password;
            },

            async submitForm() {
                this.generalError = '';

                if (!this.validateForm()) {
                    return;
                }

                this.loading = true;

                try {
                    const formData = new FormData();
                    formData.append('email', this.form.email);
                    formData.append('password', this.form.password);
                    if (this.form.remember) {
                        formData.append('remember', '1');
                    }
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

                    const response = await fetch('{{ route("login") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
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
                        if (data.errors) {
                            this.errors = data.errors;
                        } else {
                            this.generalError = data.message || 'Invalid credentials. Please try again.';
                            // Show error toast
                            if (typeof showToast === 'function') {
                                showToast(this.generalError, 'error');
                            }
                        }
                    }
                } catch (error) {
                    this.generalError = 'An error occurred. Please try again.';
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>

{{-- Alpine.js is loaded globally via app.js --}}
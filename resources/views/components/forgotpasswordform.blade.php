<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 sm:px-6 lg:px-8"
    x-data="forgotPasswordForm()">
    <div class="w-full max-w-md space-y-8">
        <!-- Header -->
        <div class="text-center">
            <a href="/">
                <img class="mx-auto h-18 w-auto" src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao" />
                <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Forgot your password?</h2>
                <p class="mt-2 text-sm text-gray-600" x-show="step === 'email'">
                    Enter your email address and we’ll send you a 6-digit code to reset your password.
                </p>
                <p class="mt-2 text-sm text-gray-600" x-show="step === 'code'">
                    Enter the 6-digit code sent to your email.
                </p>
                <p class="mt-2 text-sm text-gray-600" x-show="step === 'reset'">
                    Enter your new password below.
                </p>
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white py-8 px-6 shadow-lg rounded-lg border border-gray-200">
            <form @submit.prevent="step === 'email' ? sendEmail() : step === 'code' ? verifyCode() : resetPassword()"
                class="space-y-6">

                <!-- Step 1: Email -->
                <template x-if="step === 'email'">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email address</label>
                        <input type="email" id="email" x-model="form.email" autocomplete="email" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none
                            focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition duration-200"
                            placeholder="Enter your email" />
                    </div>
                </template>

                <!-- Step 2: Code -->
                <template x-if="step === 'code'">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">6-digit Code</label>
                        <input type="text" maxlength="6" id="code" x-model="form.code" required class="block w-full px-3 py-2 text-center text-lg tracking-widest border border-gray-300 rounded-md
                            focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500"
                            placeholder="______" />
                    </div>
                </template>

                <!-- Step 3: Reset Password -->
                <template x-if="step === 'reset'">
                    <div class="space-y-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New
                                Password</label>
                            <input type="password" id="password" x-model="form.password" @input="checkStrength" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none
                                focus:ring-2 focus:ring-violet-500 focus:border-violet-500" required />
                            <p class="text-xs mt-1" x-text="'Strength: ' + passwordStrength" :class="{
                                    'text-red-500': passwordStrength === 'Weak',
                                    'text-yellow-500': passwordStrength === 'Medium',
                                    'text-green-600': passwordStrength === 'Strong'
                                }">
                            </p>
                        </div>

                        <div>
                            <label for="password_confirmation"
                                class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" id="password_confirmation" x-model="form.password_confirmation"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none
                                focus:ring-2 focus:ring-violet-500 focus:border-violet-500" required />
                        </div>
                    </div>
                </template>

                <!-- Button with Loading Spinner -->
                <div>
                    <button type="submit" :disabled="loading"
                        class="group relative w-full flex justify-center items-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white
                        bg-violet-600 hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                        disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm hover:shadow-md">

                        <!-- Spinner -->
                        <svg x-show="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 
                                   5.291A7.962 7.962 0 014 12H0c0 3.042 
                                   1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>

                        <!-- Button text -->
                        <span x-text="loading
                            ? (step === 'email'
                                ? 'Sending...'
                                : step === 'code'
                                    ? 'Verifying...'
                                    : 'Resetting...')
                            : (step === 'email'
                                ? 'Send Code'
                                : step === 'code'
                                    ? 'Verify Code'
                                    : 'Reset Password')"></span>
                    </button>
                </div>

            </form>
        </div>

        <!-- Back to Login -->
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Remembered your password?
                <a href="/login"
                    class="font-medium text-violet-600 hover:text-violet-500 transition-colors duration-200">
                    Sign in here
                </a>
            </p>
        </div>
    </div>
</div>

<script>
    function forgotPasswordForm() {
        return {
            step: 'email',
            form: { email: '', code: '', password: '', password_confirmation: '' },
            loading: false,
            passwordStrength: '',
            async sendEmail() {
                this.loading = true;
                try {
                    const res = await fetch('/password/send-code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ email: this.form.email })
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.error || 'Request failed');
                    showToast('Verification code sent to your email!', 'success');
                    this.step = 'code';
                } catch (err) {
                    showToast(err.message, 'error');
                } finally {
                    this.loading = false;
                }
            },
            async verifyCode() {
                this.loading = true;
                try {
                    const res = await fetch('/password/verify-code', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ email: this.form.email, code: this.form.code })
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.error || 'Invalid code');
                    showToast('Code verified! You may now reset your password.', 'success');
                    this.step = 'reset';
                } catch (err) {
                    showToast(err.message, 'error');
                } finally {
                    this.loading = false;
                }
            },
            async resetPassword() {
                this.loading = true;
                try {
                    const res = await fetch('/password/reset', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(this.form)
                    });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.error || 'Reset failed');
                    showToast('Password successfully reset! Redirecting...', 'success');
                    setTimeout(() => window.location.href = '/login', 2000);
                } catch (err) {
                    showToast(err.message, 'error');
                } finally {
                    this.loading = false;
                }
            },
            checkStrength() {
                const pwd = this.form.password;
                if (!pwd) this.passwordStrength = '';
                else if (pwd.length < 8) this.passwordStrength = 'Weak';
                else if (/[A-Z]/.test(pwd) && /[0-9]/.test(pwd) && /[!@#$%^&*]/.test(pwd))
                    this.passwordStrength = 'Strong';
                else this.passwordStrength = 'Medium';
            },
        };
    }
</script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 h-full">
    <main>
        <div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8" x-data="registerForm()">
            <div class="w-full max-w-2xl space-y-8">
                <!-- Header -->
                <div class="text-center">
                    <img class="mx-auto h-18 w-auto" src="{{ asset('images/Dressupdavaologo.png') }}"
                        alt="DressUp Davao" />
                    <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Create your account</h2>
                    <p class="mt-2 text-sm text-gray-600">Join DressUp Davao and find your perfect outfit</p>
                </div>

                <!-- Progress Bar -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium"
                                    :class="currentStep >= 1 ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-600'">
                                    1
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-900">Account Details</span>
                            </div>
                            <div class="flex-1 h-1 mx-4 rounded-full"
                                :class="currentStep >= 2 ? 'bg-violet-600' : 'bg-gray-200'"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium"
                                    :class="currentStep >= 2 ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-600'">
                                    2
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-900">Preferences</span>
                            </div>
                            <div class="flex-1 h-1 mx-4 rounded-full"
                                :class="currentStep >= 3 ? 'bg-violet-600' : 'bg-gray-200'"></div>
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-full text-sm font-medium"
                                    :class="currentStep >= 3 ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-600'">
                                    3
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-900">Measurements</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form @submit.prevent="submitForm">
                        @csrf

                        <!-- Step 1: Account Details -->
                        <div x-show="currentStep === 1" class="space-y-6">
                            <div class="text-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Account Details</h3>
                                <p class="text-sm text-gray-600">Set up your email and password</p>
                            </div>

                            <!-- Full Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" x-model="form.name"
                                    @blur="validateField('name')" autocomplete="name" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                           focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                           transition-colors duration-200"
                                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.name }"
                                    placeholder="Enter your full name" />
                                <p x-show="errors.name" x-text="errors.name" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email" x-model="form.email"
                                    @blur="validateField('email')" autocomplete="email" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                           focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                           transition-colors duration-200"
                                    :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.email }"
                                    placeholder="Enter your email address" />
                                <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                        x-model="form.password" @blur="validateField('password')"
                                        autocomplete="new-password" required class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
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
                                <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-red-600">
                                </p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input :type="showConfirmPassword ? 'text' : 'password'"
                                        name="password_confirmation" id="password_confirmation"
                                        x-model="form.password_confirmation"
                                        @blur="validateField('password_confirmation')" autocomplete="new-password"
                                        required class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                               focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                               transition-colors duration-200"
                                        :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.password_confirmation }"
                                        placeholder="Confirm your password" />
                                    <button type="button" @click="showConfirmPassword = !showConfirmPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600
                                               focus:outline-none focus:text-gray-600 transition-colors duration-200">
                                        <svg x-show="!showConfirmPassword" class="h-5 w-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg x-show="showConfirmPassword" class="h-5 w-5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                <p x-show="errors.password_confirmation" x-text="errors.password_confirmation"
                                    class="mt-1 text-sm text-red-600"></p>
                            </div>

                            <!-- Navigation -->
                            <div class="flex justify-end pt-6">
                                <button type="button" @click="nextStep()" class="px-6 py-3 bg-violet-600 text-white font-medium rounded-md hover:bg-violet-700
                                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                           transition-colors duration-200">
                                    Continue
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Preferences -->
                        <div x-show="currentStep === 2" class="space-y-6">
                            <div class="text-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Style Preferences</h3>
                                <p class="text-sm text-gray-600">Tell us about your style preferences (optional)</p>
                            </div>

                            <!-- Color Preference -->
                            <div>
                                <label for="color_preference" class="block text-sm font-medium text-gray-700 mb-2">
                                    Color Preference
                                </label>
                                <select name="color_preference" id="color_preference" x-model="form.color_preference"
                                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                           focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                           transition-colors duration-200">
                                    <option value="">Choose a color preference</option>
                                    <option value="red">Red</option>
                                    <option value="blue">Blue</option>
                                    <option value="green">Green</option>
                                    <option value="black">Black</option>
                                    <option value="white">White</option>
                                    <option value="neutral">Neutral Tones</option>
                                </select>
                            </div>

                            <!-- Occasion Preference -->
                            <div>
                                <label for="style_preference_2" class="block text-sm font-medium text-gray-700 mb-2">
                                    Occasion Preference
                                </label>
                                <select name="style_preference_2" id="style_preference_2"
                                    x-model="form.style_preference_2" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                           focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                           transition-colors duration-200">
                                    <option value="">Choose an occasion preference</option>
                                    <option value="formal">Formal</option>
                                    <option value="casual">Casual</option>
                                    <option value="business">Business</option>
                                    <option value="party">Party/Events</option>
                                    <option value="everyday">Everyday Wear</option>
                                </select>
                            </div>

                            <!-- Navigation -->
                            <div class="flex justify-between pt-6">
                                <button type="button" @click="prevStep()" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50
                                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                           transition-colors duration-200">
                                    Back
                                </button>
                                <div class="flex space-x-3">
                                    <button type="button" @click="skipStep()" class="px-6 py-3 text-gray-600 font-medium rounded-md hover:text-gray-800
                                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                               transition-colors duration-200">
                                        Skip
                                    </button>
                                    <button type="button" @click="nextStep()" class="px-6 py-3 bg-violet-600 text-white font-medium rounded-md hover:bg-violet-700
                                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                               transition-colors duration-200">
                                        Continue
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Body Measurements -->
                        <div x-show="currentStep === 3" class="space-y-6">
                            <div class="text-center mb-6">
                                <h3 class="text-lg font-semibold text-gray-900">Body Measurements</h3>
                                <p class="text-sm text-gray-600">Help us recommend the perfect fit (optional)</p>
                            </div>

                            <!-- Measurements Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Chest -->
                                <div>
                                    <label for="chest" class="block text-sm font-medium text-gray-700 mb-2">
                                        Chest (inches)
                                    </label>
                                    <input type="number" name="chest" id="chest" x-model="form.measurements.chest"
                                        step="0.5" min="20" max="60" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                               focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                               transition-colors duration-200" placeholder="e.g., 36" />
                                </div>

                                <!-- Waist -->
                                <div>
                                    <label for="waist" class="block text-sm font-medium text-gray-700 mb-2">
                                        Waist (inches)
                                    </label>
                                    <input type="number" name="waist" id="waist" x-model="form.measurements.waist"
                                        step="0.5" min="20" max="50" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                               focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                               transition-colors duration-200" placeholder="e.g., 32" />
                                </div>

                                <!-- Hips -->
                                <div>
                                    <label for="hips" class="block text-sm font-medium text-gray-700 mb-2">
                                        Hips (inches)
                                    </label>
                                    <input type="number" name="hips" id="hips" x-model="form.measurements.hips"
                                        step="0.5" min="20" max="60" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                               focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                               transition-colors duration-200" placeholder="e.g., 38" />
                                </div>

                                <!-- Shoulder -->
                                <div>
                                    <label for="shoulder" class="block text-sm font-medium text-gray-700 mb-2">
                                        Shoulder (inches)
                                    </label>
                                    <input type="number" name="shoulder" id="shoulder"
                                        x-model="form.measurements.shoulder" step="0.5" min="10" max="30" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                               focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                                               transition-colors duration-200" placeholder="e.g., 16" />
                                </div>
                            </div>

                            <!-- Measurement Guide -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <h4 class="text-sm font-medium text-blue-900 mb-2">Measurement Guide:</h4>
                                <ul class="text-xs text-blue-800 space-y-1">
                                    <li><strong>Chest:</strong> Measure around the fullest part of your chest</li>
                                    <li><strong>Waist:</strong> Measure around your natural waistline</li>
                                    <li><strong>Hips:</strong> Measure around the fullest part of your hips</li>
                                    <li><strong>Shoulder:</strong> Measure from shoulder point to shoulder point across
                                        your back</li>
                                </ul>
                            </div>

                            <!-- Navigation -->
                            <div class="flex justify-between pt-6">
                                <button type="button" @click="prevStep()" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50
                                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                           transition-colors duration-200">
                                    Back
                                </button>
                                <div class="flex space-x-3">
                                    <button type="button" @click="skipStep()" class="px-6 py-3 text-gray-600 font-medium rounded-md hover:text-gray-800
                                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                               transition-colors duration-200">
                                        Skip
                                    </button>
                                    <button type="submit" :disabled="loading"
                                        class="px-6 py-3 bg-violet-600 text-white font-medium rounded-md hover:bg-violet-700
                                               focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                               disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200">
                                        <span x-show="!loading">Create Account</span>
                                        <span x-show="loading" class="flex items-center">
                                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Creating Account...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Error Messages -->
                        <div x-show="generalError" class="rounded-md bg-red-50 p-4 border border-red-200 mt-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Registration failed</h3>
                                    <p class="mt-1 text-sm text-red-700" x-text="generalError"></p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-sm text-gray-600">
                        Already have an account?
                        <a href="/login"
                            class="font-medium text-violet-600 hover:text-violet-500 transition-colors duration-200">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <script>
            function registerForm() {
                return {
                    currentStep: 1,
                    form: {
                        name: '',
                        email: '',
                        password: '',
                        password_confirmation: '',
                        color_preference: '',
                        style_preference_2: '',
                        measurements: {
                            chest: '',
                            waist: '',
                            hips: '',
                            shoulder: ''
                        }
                    },
                    errors: {},
                    generalError: '',
                    showPassword: false,
                    showConfirmPassword: false,
                    loading: false,

                    validateField(field) {
                        this.errors[field] = '';

                        switch (field) {
                            case 'name':
                                if (!this.form.name) {
                                    this.errors.name = 'Full name is required';
                                } else if (this.form.name.length < 2) {
                                    this.errors.name = 'Name must be at least 2 characters';
                                }
                                break;
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
                                } else if (this.form.password.length < 8) {
                                    this.errors.password = 'Password must be at least 8 characters';
                                }
                                break;
                            case 'password_confirmation':
                                if (!this.form.password_confirmation) {
                                    this.errors.password_confirmation = 'Password confirmation is required';
                                } else if (this.form.password !== this.form.password_confirmation) {
                                    this.errors.password_confirmation = 'Passwords do not match';
                                }
                                break;
                        }
                    },

                    isValidEmail(email) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        return emailRegex.test(email);
                    },

                    validateStep1() {
                        this.validateField('name');
                        this.validateField('email');
                        this.validateField('password');
                        this.validateField('password_confirmation');
                        return !this.errors.name && !this.errors.email && !this.errors.password && !this.errors.password_confirmation;
                    },

                    nextStep() {
                        if (this.currentStep === 1) {
                            if (!this.validateStep1()) {
                                return;
                            }
                        }
                        if (this.currentStep < 3) {
                            this.currentStep++;
                        }
                    },

                    prevStep() {
                        if (this.currentStep > 1) {
                            this.currentStep--;
                        }
                    },

                    skipStep() {
                        if (this.currentStep === 2) {
                            // Clear preferences
                            this.form.color_preference = '';
                            this.form.style_preference_2 = '';
                        } else if (this.currentStep === 3) {
                            // Clear measurements
                            this.form.measurements = {
                                chest: '',
                                waist: '',
                                hips: '',
                                shoulder: ''
                            };
                        }
                        this.nextStep();
                    },

                    async submitForm() {
                        this.generalError = '';

                        if (!this.validateStep1()) {
                            this.currentStep = 1;
                            return;
                        }

                        this.loading = true;

                        try {
                            const formData = new FormData();
                            formData.append('name', this.form.name);
                            formData.append('email', this.form.email);
                            formData.append('password', this.form.password);
                            formData.append('password_confirmation', this.form.password_confirmation);
                            formData.append('color_preference', this.form.color_preference);
                            formData.append('style_preference_2', this.form.style_preference_2);

                            // Add measurements
                            formData.append('chest', this.form.measurements.chest);
                            formData.append('waist', this.form.measurements.waist);
                            formData.append('hips', this.form.measurements.hips);
                            formData.append('shoulder', this.form.measurements.shoulder);

                            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');

                            const response = await fetch('{{ route("register.submit") }}', {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            const data = await response.json();

                            if (response.ok && data.success) {
                                window.location.href = data.redirect || '/';
                            } else {
                                if (data.errors) {
                                    this.errors = data.errors;
                                    // Go back to step 1 if there are validation errors
                                    this.currentStep = 1;
                                } else {
                                    this.generalError = data.message || 'Registration failed. Please try again.';
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

        {{-- Alpine.js --}}
        <script src="//unpkg.com/alpinejs" defer></script>

    </main>
</body>

</html>
@php
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');

    $errors = session('errors') ?: new \Illuminate\Support\MessageBag();
    $generalError = session('error') ?? '';
@endphp

<div class="min-h-screen bg-white flex flex-col" x-data="mobileRegister()">
    <!-- Header with Back Button -->
    <div class="sticky top-0 z-50 bg-white border-b border-gray-200 px-4 py-3">
        <div class="flex items-center justify-between">
            <a href="{{ $isMobileApp ? '/login?app=1&mobile_nav=true' : '/login' }}"
                class="flex items-center text-gray-600 active:scale-95 transition-transform duration-200">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
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
    <div class="flex-1 px-4 py-6 overflow-y-auto no-scrollbar">
        <div class="max-w-md mx-auto">
            <!-- Welcome Section -->
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Create Account</h1>
                <p class="text-gray-600">Join DressUp Davao and find your perfect outfit</p>
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4" @submit.prevent="submitForm"
                autocomplete="off">
                @csrf

                <!-- Hidden Turnstile field for mobile (bypass) -->
                <input type="hidden" name="cf-turnstile-response" value="mobile-bypass-token">

                @if($isMobileApp)
                    <!-- Mobile identifiers -->
                    <input type="hidden" name="app" value="1">
                    <input type="hidden" name="mobile_nav" value="true">
                @endif

                <!-- Agreement Section -->
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                    <div class="flex items-start space-x-3">
                        <input type="checkbox" id="nda_agreement" name="nda_agreement" x-model="form.nda_agreement"
                            required class="mt-1 w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                        <label for="nda_agreement" class="text-sm text-gray-700">
                            <span class="font-medium">I agree to the Confidentiality & Data Consent Agreement.</span>
                            <span class="text-red-500 ml-1">*</span>
                            <a href="javascript:void(0)" @click="showAgreementModal = true"
                                class="block mt-1 text-purple-600 hover:text-purple-700 underline text-xs">
                                View full agreement
                            </a>
                        </label>
                    </div>
                    <p x-show="errors.nda_agreement" x-text="errors.nda_agreement" class="mt-2 text-sm text-red-600">
                    </p>
                </div>

                <!-- Full Name -->
                <div>
                    <label for="mobile-name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="mobile-name" x-model="form.name"
                        :class="{ 'border-red-300': errors.name }" value="{{ old('name') }}" autocomplete="name"
                        required class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50
                                  focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                  placeholder-gray-500 text-gray-900 text-base-mobile
                                  transition-all duration-200" placeholder="Enter your full name">
                    <p x-show="errors.name" x-text="errors.name" class="mt-2 text-sm text-red-600"></p>
                </div>

                <!-- Gender -->
                <div>
                    <label for="mobile-gender" class="block text-sm font-medium text-gray-700 mb-2">
                        Gender <span class="text-red-500">*</span>
                    </label>
                    <select name="gender" id="mobile-gender" x-model="form.gender"
                        :class="{ 'border-red-300': errors.gender }" required class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50
                                   focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                   text-gray-900 text-base-mobile
                                   transition-all duration-200">
                        <option value="">Select your gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                    <p x-show="errors.gender" x-text="errors.gender" class="mt-2 text-sm text-red-600"></p>
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="mobile-phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone_number" id="mobile-phone" x-model="form.phone_number"
                        @input="formatPhoneNumber" :class="{ 'border-red-300': errors.phone_number }"
                        value="{{ old('phone_number') }}" autocomplete="tel" required class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50
                                  focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                  placeholder-gray-500 text-gray-900 text-base-mobile
                                  transition-all duration-200" placeholder="e.g., 09123456789">
                    <p x-show="errors.phone_number" x-text="errors.phone_number" class="mt-2 text-sm text-red-600"></p>
                </div>

                <!-- Email -->
                <div>
                    <label for="mobile-email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="email" name="email" id="mobile-email" x-model="form.email"
                            @input.debounce.500ms="checkEmailUnique" :class="{ 'border-red-300': errors.email || emailStatus === 'taken', 
                                        'border-green-300': emailStatus === 'available' }" value="{{ old('email') }}"
                            autocomplete="email" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl bg-gray-50
                                      focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                      placeholder-gray-500 text-gray-900 text-base-mobile
                                      transition-all duration-200" placeholder="you@example.com">
                    </div>
                    <p x-show="emailStatus === 'checking'" class="mt-2 text-sm text-blue-600">Checking availability...
                    </p>
                    <p x-show="emailStatus === 'taken'" class="mt-2 text-sm text-red-600">Email already in use.</p>
                    <p x-show="emailStatus === 'available'" class="mt-2 text-sm text-green-600">Email is available!</p>
                    <p x-show="errors.email" x-text="errors.email" class="mt-2 text-sm text-red-600"></p>
                </div>

                <!-- Password -->
                <div>
                    <label for="mobile-password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input :type="showPassword ? 'text' : 'password'" name="password" id="mobile-password"
                            x-model="form.password" @input="checkPasswordStrength"
                            :class="{ 'border-red-300': errors.password }" autocomplete="new-password" required class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl bg-gray-50
                                      focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                      placeholder-gray-500 text-gray-900 text-base-mobile
                                      transition-all duration-200" placeholder="Enter your password">
                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
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

                    <!-- Password Requirements -->
                    <div class="mt-2 space-y-1">
                        <div class="flex items-center text-xs"
                            :class="form.password.length >= 8 ? 'text-green-600' : 'text-gray-500'">
                            <span x-text="form.password.length >= 8 ? '✓' : '•'" class="mr-2"></span>
                            At least 8 characters
                        </div>
                        <div class="flex items-center text-xs"
                            :class="/[A-Z]/.test(form.password) ? 'text-green-600' : 'text-gray-500'">
                            <span x-text="/[A-Z]/.test(form.password) ? '✓' : '•'" class="mr-2"></span>
                            At least one uppercase letter
                        </div>
                        <div class="flex items-center text-xs"
                            :class="/[^A-Za-z0-9]/.test(form.password) ? 'text-green-600' : 'text-gray-500'">
                            <span x-text="/[^A-Za-z0-9]/.test(form.password) ? '✓' : '•'" class="mr-2"></span>
                            At least one special character
                        </div>
                    </div>

                    <!-- Password Strength -->
                    <div class="mt-2">
                        <div class="w-full h-1.5 rounded-full bg-gray-200">
                            <div class="h-1.5 rounded-full transition-all duration-300" :class="passwordStrength.class"
                                :style="{ width: passwordStrength.percent + '%' }"></div>
                        </div>
                        <p class="text-xs mt-1" :class="passwordStrength.textClass" x-text="passwordStrength.label"></p>
                    </div>

                    <p x-show="errors.password" x-text="errors.password" class="mt-2 text-sm text-red-600"></p>
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="mobile-confirm-password" class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <input :type="showConfirmPassword ? 'text' : 'password'" name="password_confirmation"
                            id="mobile-confirm-password" x-model="form.password_confirmation"
                            :class="{ 'border-red-300': errors.password_confirmation }" autocomplete="new-password"
                            required class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl bg-gray-50
                                      focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                      placeholder-gray-500 text-gray-900 text-base-mobile
                                      transition-all duration-200" placeholder="Confirm your password">
                        <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg x-show="!showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" />
                            </svg>
                        </button>
                    </div>
                    <p x-show="errors.password_confirmation" x-text="errors.password_confirmation"
                        class="mt-2 text-sm text-red-600"></p>
                </div>

                <!-- Style Preferences Section -->
                <div class="pt-4 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Style Preferences (Optional)</h3>

                    <!-- Color Preference -->
                    <div class="mb-4">
                        <label for="mobile-color" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Color
                        </label>
                        <select name="color_preference" id="mobile-color" x-model="form.color_preference" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50
                                       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                       text-gray-900 text-base-mobile
                                       transition-all duration-200">
                            <option value="">Choose a color preference</option>
                            <option value="red">Red</option>
                            <option value="blue">Blue</option>
                            <option value="green">Green</option>
                            <option value="black">Black</option>
                            <option value="white">White</option>
                            <option value="purple">Purple</option>
                            <option value="pink">Pink</option>
                            <option value="neutral">Neutral Tones</option>
                            <option value="earth">Earth Tones</option>
                            <option value="bright">Bright Colors</option>
                        </select>
                    </div>

                    <!-- Occasion Preference -->
                    <div class="mb-4">
                        <label for="mobile-occasion" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Occasion
                        </label>
                        <select name="occasion_preference" id="mobile-occasion" x-model="form.occasion_preference"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50
                                       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                       text-gray-900 text-base-mobile
                                       transition-all duration-200">
                            <option value="">Choose an occasion preference</option>
                            <option value="formal">Formal</option>
                            <option value="casual">Casual</option>
                            <option value="business">Business</option>
                            <option value="party">Party/Events</option>
                            <option value="wedding">Wedding</option>
                            <option value="everyday">Everyday Wear</option>
                            <option value="gala">Gala</option>
                            <option value="prom">Prom</option>
                        </select>
                    </div>

                    <!-- Fabric Preference -->
                    <div class="mb-4">
                        <label for="mobile-fabric" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Fabric
                        </label>
                        <select name="fabric_preference" id="mobile-fabric" x-model="form.fabric_preference" class="block w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-50
                                       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                       text-gray-900 text-base-mobile
                                       transition-all duration-200">
                            <option value="">Choose a fabric preference</option>
                            <option value="cotton">Cotton</option>
                            <option value="silk">Silk</option>
                            <option value="linen">Linen</option>
                            <option value="wool">Wool</option>
                            <option value="polyester">Polyester</option>
                            <option value="velvet">Velvet</option>
                            <option value="satin">Satin</option>
                            <option value="chiffon">Chiffon</option>
                            <option value="denim">Denim</option>
                            <option value="lace">Lace</option>
                        </select>
                    </div>
                </div>

                <!-- Body Measurements Section (Optional) -->
                <div class="pt-4 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Body Measurements (Optional)</h3>
                    <p class="text-sm text-gray-600 mb-4">Help us recommend the perfect fit</p>

                    <!-- Unit Toggle -->
                    <div class="flex items-center justify-center space-x-3 mb-4">
                        <span class="text-sm font-medium text-gray-700">Units:</span>
                        <div class="flex bg-gray-100 rounded-lg p-1">
                            <button type="button" @click="measurementUnit = 'inches'"
                                :class="measurementUnit === 'inches' ? 'bg-white shadow-sm text-purple-700' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1 text-sm font-medium rounded-md transition-all duration-200">
                                Inches
                            </button>
                            <button type="button" @click="measurementUnit = 'centimeters'"
                                :class="measurementUnit === 'centimeters' ? 'bg-white shadow-sm text-purple-700' : 'text-gray-600 hover:text-gray-900'"
                                class="px-3 py-1 text-sm font-medium rounded-md transition-all duration-200">
                                Centimeters
                            </button>
                        </div>
                    </div>

                    <!-- Measurements Grid -->
                    <div class="grid grid-cols-2 gap-3">
                        <!-- Chest -->
                        <div>
                            <label for="mobile-chest" class="block text-xs font-medium text-gray-700 mb-1">
                                Chest (<span x-text="measurementUnit === 'inches' ? 'in' : 'cm'"></span>)
                            </label>
                            <input :type="measurementUnit === 'inches' ? 'number' : 'text'"
                                :name="measurementUnit === 'inches' ? 'chest' : 'chest_cm'" id="mobile-chest"
                                x-model="measurementUnit === 'inches' ? form.measurements.chest : chestCm"
                                @input="measurementUnit === 'centimeters' ? updateMeasurementFromCm('chest', $event.target.value) : (form.measurements.chest = $event.target.value)"
                                :step="measurementUnit === 'inches' ? '0.5' : '0.1'" class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50
                                          focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                          text-gray-900 text-sm
                                          transition-all duration-200"
                                :placeholder="measurementUnit === 'inches' ? 'e.g., 36' : 'e.g., 91.5'">
                        </div>

                        <!-- Waist -->
                        <div>
                            <label for="mobile-waist" class="block text-xs font-medium text-gray-700 mb-1">
                                Waist (<span x-text="measurementUnit === 'inches' ? 'in' : 'cm'"></span>)
                            </label>
                            <input :type="measurementUnit === 'inches' ? 'number' : 'text'"
                                :name="measurementUnit === 'inches' ? 'waist' : 'waist_cm'" id="mobile-waist"
                                x-model="measurementUnit === 'inches' ? form.measurements.waist : waistCm"
                                @input="measurementUnit === 'centimeters' ? updateMeasurementFromCm('waist', $event.target.value) : (form.measurements.waist = $event.target.value)"
                                :step="measurementUnit === 'inches' ? '0.5' : '0.1'" class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50
                                          focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                          text-gray-900 text-sm
                                          transition-all duration-200"
                                :placeholder="measurementUnit === 'inches' ? 'e.g., 32' : 'e.g., 81'">
                        </div>

                        <!-- Hips -->
                        <div>
                            <label for="mobile-hips" class="block text-xs font-medium text-gray-700 mb-1">
                                Hips (<span x-text="measurementUnit === 'inches' ? 'in' : 'cm'"></span>)
                            </label>
                            <input :type="measurementUnit === 'inches' ? 'number' : 'text'"
                                :name="measurementUnit === 'inches' ? 'hips' : 'hips_cm'" id="mobile-hips"
                                x-model="measurementUnit === 'inches' ? form.measurements.hips : hipsCm"
                                @input="measurementUnit === 'centimeters' ? updateMeasurementFromCm('hips', $event.target.value) : (form.measurements.hips = $event.target.value)"
                                :step="measurementUnit === 'inches' ? '0.5' : '0.1'" class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50
                                          focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                          text-gray-900 text-sm
                                          transition-all duration-200"
                                :placeholder="measurementUnit === 'inches' ? 'e.g., 38' : 'e.g., 96.5'">
                        </div>

                        <!-- Shoulder -->
                        <div>
                            <label for="mobile-shoulder" class="block text-xs font-medium text-gray-700 mb-1">
                                Shoulder (<span x-text="measurementUnit === 'inches' ? 'in' : 'cm'"></span>)
                            </label>
                            <input :type="measurementUnit === 'inches' ? 'number' : 'text'"
                                :name="measurementUnit === 'inches' ? 'shoulder' : 'shoulder_cm'" id="mobile-shoulder"
                                x-model="measurementUnit === 'inches' ? form.measurements.shoulder : shoulderCm"
                                @input="measurementUnit === 'centimeters' ? updateMeasurementFromCm('shoulder', $event.target.value) : (form.measurements.shoulder = $event.target.value)"
                                :step="measurementUnit === 'inches' ? '0.5' : '0.1'" class="block w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50
                                          focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent
                                          text-gray-900 text-sm
                                          transition-all duration-200"
                                :placeholder="measurementUnit === 'inches' ? 'e.g., 16' : 'e.g., 40.5'">
                        </div>
                    </div>

                    <!-- Measurement Guide -->
                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <h4 class="text-xs font-medium text-blue-900 mb-1">Measurement Guide:</h4>
                        <ul class="text-xs text-blue-800 space-y-0.5">
                            <li>• <strong>Chest:</strong> Around fullest part</li>
                            <li>• <strong>Waist:</strong> Around natural waistline</li>
                            <li>• <strong>Hips:</strong> Around fullest part</li>
                            <li>• <strong>Shoulder:</strong> Across back shoulder to shoulder</li>
                        </ul>
                    </div>
                </div>

                <!-- Error Messages -->
                <div x-show="generalError" class="rounded-xl bg-red-50 p-4 border border-red-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700" x-text="generalError"></p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6">
                    <button type="submit" :disabled="isLoading || !form.nda_agreement" class="w-full py-4 px-6 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl
                                   hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2
                                   transition-all duration-200 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed
                                   flex items-center justify-center">
                        <svg x-show="isLoading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-show="!isLoading">Create Account</span>
                        <span x-show="isLoading">Creating Account...</span>
                    </button>
                </div>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">Already have an account?</span>
                </div>
            </div>

            <!-- Login Link -->
            <a href="/login{{ $isMobileApp ? '?app=1&mobile_nav=true' : '' }}" class="block w-full py-4 px-6 border-2 border-purple-600 text-purple-600 font-semibold rounded-xl
                      hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2
                      transition-all duration-200 active:scale-95 text-center">
                Sign In
            </a>
        </div>
    </div>

    <!-- Bottom Navigation Spacer (for mobile app) -->
    @if($isMobileApp)
        <div class="h-16"></div>
    @endif

    <!-- Agreement Modal -->
    <div x-show="showAgreementModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full max-h-[80vh] flex flex-col">
            <!-- Modal Header -->
            <div
                class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-2xl">
                <h3 class="text-lg font-semibold text-gray-900">Confidentiality Agreement</h3>
                <button @click="showAgreementModal = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="flex-1 overflow-y-auto p-6 text-sm text-gray-700 space-y-4">
                <h4 class="text-lg font-semibold text-gray-900 mb-3">CUSTOMER CONFIDENTIALITY & DATA CONSENT AGREEMENT
                </h4>

                <p><strong>Effective Date:</strong> <span x-text="new Date().toLocaleDateString()"></span></p>

                <p><strong>1. Purpose:</strong> This Agreement explains how confidential information is handled between
                    <strong>DressUp Davao</strong> ("Company") and the registering user ("User") while using the
                    platform.</p>

                <p><strong>2. What Counts as Confidential Information:</strong> "Confidential Information" includes:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>User personal information (name, email, birthdate, address, body measurements, preferences)</li>
                    <li>User activities (favorites, messages, inquiries, browsing behavior)</li>
                    <li>Non-public shop information shared inside the platform</li>
                    <li>Platform features, algorithms, recommendation logic, pricing structures</li>
                    <li>Any information not intended for public disclosure</li>
                </ul>

                <p><strong>3. Obligations of DressUp Davao:</strong> DressUp Davao agrees to:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Protect User data using reasonable security measures</li>
                    <li>Not sell or disclose User information to unauthorized third parties</li>
                    <li>Use body measurement data only for recommendation systems and shop-related assistance</li>
                    <li>Comply with applicable Philippine data privacy laws</li>
                </ul>

                <p><strong>4. Obligations of the User:</strong> The User agrees to:</p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Keep non-public shop or platform information confidential</li>
                    <li>Not misuse platform features, data, or communication tools</li>
                    <li>Not copy, distribute, or attempt to reverse-engineer platform internals</li>
                </ul>

                <p><strong>5. Consent for Body Measurement Processing:</strong> By registering, the User consents to:
                </p>
                <ul class="list-disc pl-5 space-y-1">
                    <li>Their submitted measurements will be used for automated gown/suit recommendations</li>
                    <li>Shops they engage with may view relevant measurement data for inquiries or rentals</li>
                </ul>

                <p><strong>6. Relationship:</strong> This Agreement does not create an employer/employee, agency, or
                    partnership relationship between the User and DressUp Davao.</p>

                <p><strong>7. Continuation of Confidentiality:</strong> Confidentiality obligations continue even after
                    the User deletes their account, except for information the User has already made public.</p>

                <p><strong>8. Legal Disclosures:</strong> If DressUp Davao is legally required to disclose information,
                    the User will be informed whenever possible.</p>

                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                    <p class="text-sm text-yellow-800">
                        <strong>Important:</strong> By checking the box on the registration form, you acknowledge that
                        you have read, understood, and agree to this Confidentiality & Data Consent Agreement.
                    </p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="sticky bottom-0 bg-white border-t border-gray-200 px-6 py-4 rounded-b-2xl">
                <button @click="showAgreementModal = false" class="w-full py-3 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700
                               focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2
                               transition-all duration-200">
                    I Understand
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('mobileRegister', () => ({
            form: {
                nda_agreement: false,
                name: '{{ old("name") }}',
                email: '{{ old("email") }}',
                gender: '{{ old("gender") }}',
                phone_number: '{{ old("phone_number") }}',
                password: '',
                password_confirmation: '',
                color_preference: '{{ old("color_preference") }}',
                occasion_preference: '{{ old("occasion_preference") }}',
                fabric_preference: '{{ old("fabric_preference") }}',
                measurements: {
                    chest: '{{ old("chest") }}',
                    waist: '{{ old("waist") }}',
                    hips: '{{ old("hips") }}',
                    shoulder: '{{ old("shoulder") }}'
                }
            },
            chestCm: '',
            waistCm: '',
            hipsCm: '',
            shoulderCm: '',
            measurementUnit: 'inches',
            errors: {},
            showPassword: false,
            showConfirmPassword: false,
            isLoading: false,
            generalError: '{{ $generalError }}',
            emailStatus: '',
            passwordStrength: { label: '', percent: 0, class: '', textClass: '' },
            showAgreementModal: false,

            init() {
                // Set up centimeter values based on existing inch values
                if (this.form.measurements.chest) {
                    this.chestCm = this.convertToCm(this.form.measurements.chest);
                }
                if (this.form.measurements.waist) {
                    this.waistCm = this.convertToCm(this.form.measurements.waist);
                }
                if (this.form.measurements.hips) {
                    this.hipsCm = this.convertToCm(this.form.measurements.hips);
                }
                if (this.form.measurements.shoulder) {
                    this.shoulderCm = this.convertToCm(this.form.measurements.shoulder);
                }

                // Add touch feedback
                this.addTouchFeedback();
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

            // Conversion functions
            convertToCm(inches) {
                if (!inches || inches === '' || isNaN(inches)) return '';
                return (parseFloat(inches) * 2.54).toFixed(1);
            },

            convertToInches(cm) {
                if (!cm || cm === '' || isNaN(cm)) return '';
                return (parseFloat(cm) / 2.54).toFixed(1);
            },

            // Update measurement when user inputs in centimeters
            updateMeasurementFromCm(field, cmValue) {
                if (!cmValue || cmValue === '' || isNaN(cmValue)) {
                    this.form.measurements[field] = '';
                    this[field + 'Cm'] = '';
                    return;
                }

                // Convert to inches and store
                const inchesValue = (parseFloat(cmValue) / 2.54).toFixed(1);
                this.form.measurements[field] = inchesValue;

                // Update the display value (in case of rounding)
                this[field + 'Cm'] = cmValue;
            },

            formatPhoneNumber() {
                // Remove all non-digit characters
                let phone = this.form.phone_number.replace(/\D/g, '');

                // Limit to 11 digits (Philippine mobile number format)
                if (phone.length > 11) {
                    phone = phone.substring(0, 11);
                }

                // Update the model with cleaned number
                this.form.phone_number = phone;
            },

            async checkEmailUnique() {
                this.emailStatus = 'checking';
                if (!this.isValidEmail(this.form.email)) {
                    this.emailStatus = '';
                    return;
                }
                try {
                    const res = await fetch(`/check-email?email=${encodeURIComponent(this.form.email)}`);
                    const data = await res.json();
                    this.emailStatus = data.exists ? 'taken' : 'available';
                } catch (error) {
                    this.emailStatus = '';
                    console.error('Error checking email:', error);
                }
            },

            checkPasswordStrength() {
                const pwd = this.form.password;
                let score = 0;
                if (pwd.length >= 8) score += 25;
                if (/[A-Z]/.test(pwd)) score += 25;
                if (/[0-9]/.test(pwd)) score += 25;
                if (/[^A-Za-z0-9]/.test(pwd)) score += 25;

                if (score <= 25) this.passwordStrength = { label: 'Weak', percent: 25, class: 'bg-red-500', textClass: 'text-red-600' };
                else if (score <= 50) this.passwordStrength = { label: 'Moderate', percent: 50, class: 'bg-yellow-400', textClass: 'text-yellow-600' };
                else if (score <= 75) this.passwordStrength = { label: 'Strong', percent: 75, class: 'bg-green-500', textClass: 'text-green-600' };
                else this.passwordStrength = { label: 'Very Strong', percent: 100, class: 'bg-emerald-600', textClass: 'text-emerald-700' };
            },

            validateField(field) {
                this.errors[field] = '';

                switch (field) {
                    case 'nda_agreement':
                        if (!this.form.nda_agreement) {
                            this.errors.nda_agreement = 'You must agree to the Agreement to continue';
                        }
                        break;
                    case 'name':
                        if (!this.form.name) {
                            this.errors.name = 'Full name is required';
                        } else if (this.form.name.length < 2) {
                            this.errors.name = 'Name must be at least 2 characters';
                        }
                        break;
                    case 'gender':
                        if (!this.form.gender) {
                            this.errors.gender = 'Gender is required';
                        } else if (!['Male', 'Female'].includes(this.form.gender)) {
                            this.errors.gender = 'Please select a valid gender';
                        }
                        break;
                    case 'phone_number':
                        if (!this.form.phone_number) {
                            this.errors.phone_number = 'Phone number is required';
                        } else if (!/^\d+$/.test(this.form.phone_number)) {
                            this.errors.phone_number = 'Phone number must contain only numbers';
                        } else if (this.form.phone_number.length < 10) {
                            this.errors.phone_number = 'Phone number must be at least 10 digits';
                        } else if (this.form.phone_number.length > 15) {
                            this.errors.phone_number = 'Phone number cannot exceed 15 digits';
                        } else if (!/^(09|\+639)\d{9}$/.test(this.form.phone_number) && this.form.phone_number.length === 11) {
                            this.errors.phone_number = 'Please enter a valid Philippine mobile number (09XXXXXXXXX)';
                        }
                        break;
                    case 'email':
                        if (!this.form.email) {
                            this.errors.email = 'Email is required';
                        } else if (!this.isValidEmail(this.form.email)) {
                            this.errors.email = 'Please enter a valid email address';
                        } else if (this.emailStatus === 'taken') {
                            this.errors.email = 'Email is already in use';
                        }
                        break;
                    case 'password':
                        if (!this.form.password) {
                            this.errors.password = 'Password is required';
                        } else if (this.form.password.length < 8) {
                            this.errors.password = 'Password must be at least 8 characters';
                        } else if (!/[A-Z]/.test(this.form.password)) {
                            this.errors.password = 'Password must contain at least one uppercase letter';
                        } else if (!/[^A-Za-z0-9]/.test(this.form.password)) {
                            this.errors.password = 'Password must contain at least one special character (!@#$%^&* etc.)';
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

            validateForm() {
                // Clear all errors
                this.errors = {};
                this.generalError = '';

                // Validate required fields
                this.validateField('nda_agreement');
                this.validateField('name');
                this.validateField('gender');
                this.validateField('phone_number');
                this.validateField('email');
                this.validateField('password');
                this.validateField('password_confirmation');

                // Check if there are any errors
                const hasErrors = Object.keys(this.errors).some(key => this.errors[key] !== '');

                // Additional email availability check
                if (this.emailStatus === 'taken' && !this.errors.email) {
                    this.errors.email = 'Email is already in use';
                    return false;
                }

                if (this.emailStatus === 'checking') {
                    this.errors.email = 'Please wait while we check email availability';
                    return false;
                }

                return !hasErrors;
            },

            async submitForm() {
                if (!this.form.nda_agreement) {
                    this.errors.nda_agreement = 'You must agree to the Agreement to register';
                    if (typeof showToast === 'function') {
                        showToast('Please agree to the Confidentiality Agreement', 'error');
                    }
                    return;
                }

                if (!this.validateForm()) {
                    if (typeof showToast === 'function') {
                        showToast('Please check your information and try again.', 'error');
                    }
                    return;
                }

                this.isLoading = true;

                try {
                    const formData = new FormData();
                    formData.append('nda_agreement', this.form.nda_agreement ? '1' : '0');
                    formData.append('name', this.form.name);
                    formData.append('email', this.form.email);
                    formData.append('gender', this.form.gender);
                    formData.append('phone_number', this.form.phone_number);
                    formData.append('password', this.form.password);
                    formData.append('password_confirmation', this.form.password_confirmation);

                    // Add preferences if provided
                    if (this.form.color_preference) {
                        formData.append('color_preference', this.form.color_preference);
                    }
                    if (this.form.occasion_preference) {
                        formData.append('occasion_preference', this.form.occasion_preference);
                    }
                    if (this.form.fabric_preference) {
                        formData.append('fabric_preference', this.form.fabric_preference);
                    }

                    // Add measurements if provided
                    if (this.form.measurements.chest) {
                        formData.append('chest', this.form.measurements.chest);
                    }
                    if (this.form.measurements.waist) {
                        formData.append('waist', this.form.measurements.waist);
                    }
                    if (this.form.measurements.hips) {
                        formData.append('hips', this.form.measurements.hips);
                    }
                    if (this.form.measurements.shoulder) {
                        formData.append('shoulder', this.form.measurements.shoulder);
                    }

                    // Add mobile bypass token
                    formData.append('cf-turnstile-response', 'mobile-bypass-token');

                    @if($isMobileApp)
                        formData.append('app', '1');
                        formData.append('mobile_nav', 'true');
                    @endif

                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    formData.append('_token', csrfToken);

                    const response = await fetch('{{ route("register.submit") }}', {
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
                            showToast(data.message || 'Registration successful! Welcome!', 'success');
                            setTimeout(() => {
                                window.location.href = data.redirect || '/';
                            }, 1000);
                        } else {
                            window.location.href = data.redirect || '/';
                        }
                    } else {
                        // Handle errors
                        if (data.errors) {
                            this.errors = data.errors;
                            this.generalError = 'Please check your information and try again.';
                        } else {
                            this.generalError = data.message || 'Registration failed. Please try again.';
                        }

                        if (typeof showToast === 'function') {
                            showToast(this.generalError, 'error');
                        }
                    }
                } catch (error) {
                    console.error('Registration error:', error);
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
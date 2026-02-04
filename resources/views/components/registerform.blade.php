<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8" x-data="registerForm()">
    <div class="w-full max-w-2xl space-y-8">
        <!-- Header -->
        <div class="text-center">
            <a href="/">
                <img class="mx-auto h-18 w-auto" src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao" />
                <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900">Create your account</h2>
                <p class="mt-2 text-sm text-gray-600">Join DressUp Davao and find your perfect outfit</p>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <form @submit.prevent="submitForm" autocomplete="off">
                @csrf
                <!-- Step 0: Agreement (First Step) -->
                <div x-show="currentStep === 0" class="space-y-6">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Agreement</h3>
                        <p class="text-sm text-gray-600">Please review and agree to our terms before proceeding</p>
                    </div>

                    <!-- Agreement Section -->
                    <div class="border border-gray-200 rounded-lg p-6">
                        <!-- Agreement Trigger -->
                        <div class="text-center mb-4">
                            <button type="button" @click="showAgreement = !showAgreement"
                                class="text-violet-600 hover:text-violet-700 font-medium underline focus:outline-none focus:ring-2 focus:ring-violet-500 rounded">
                                <span
                                    x-text="showAgreement ? 'Hide Agreement' : 'Click to view Confidentiality & Data Consent Agreement'"></span>
                            </button>
                        </div>

                        <!-- NDA Content - Collapsible -->
                        <div x-show="showAgreement" x-transition
                            class="bg-gray-50 border border-gray-200 rounded-lg p-6 max-h-96 overflow-y-auto mb-4">
                            <div class="prose prose-sm max-w-none">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">CUSTOMER CONFIDENTIALITY & DATA
                                    CONSENT
                                    AGREEMENT</h4>

                                <p class="text-sm text-gray-700 mb-4">
                                    <strong>Effective Date:</strong>
                                    <span x-text="new Date().toLocaleDateString()"></span>
                                </p>

                                <div class="space-y-4 text-sm text-gray-700">
                                    <p><strong>1. Purpose:</strong>
                                        This Agreement ("Agreement") explains how confidential information is handled
                                        between
                                        <strong>DressUp Davao</strong> ("Company") and the registering user ("User")
                                        while
                                        using the platform,
                                        including browsing products, marking favorites, chatting with shop owners,
                                        submitting inquiries,
                                        and providing body measurements for product recommendations.
                                    </p>

                                    <p><strong>2. What Counts as Confidential Information:</strong>
                                        "Confidential Information" includes the following:
                                    </p>
                                    <ul class="list-disc pl-6 space-y-2">
                                        <li>User personal information (name, email, birthdate, address, body
                                            measurements,
                                            preferences)</li>
                                        <li>User activities (favorites, messages, inquiries, browsing behavior)</li>
                                        <li>Non-public shop information shared inside the platform</li>
                                        <li>Platform features, algorithms, recommendation logic, pricing structures</li>
                                        <li>Any information not intended for public disclosure</li>
                                    </ul>

                                    <p><strong>3. Obligations of DressUp Davao:</strong>
                                        DressUp Davao agrees to:
                                    </p>
                                    <ul class="list-disc pl-6 space-y-2">
                                        <li>Protect User data using reasonable security measures</li>
                                        <li>Not sell or disclose User information to unauthorized third parties</li>
                                        <li>Use body measurement data only for recommendation systems and shop-related
                                            assistance</li>
                                        <li>Comply with applicable Philippine data privacy laws</li>
                                    </ul>

                                    <p><strong>4. Obligations of the User:</strong>
                                        The User agrees to:
                                    </p>
                                    <ul class="list-disc pl-6 space-y-2">
                                        <li>Keep non-public shop or platform information confidential</li>
                                        <li>Not misuse platform features, data, or communication tools</li>
                                        <li>Not copy, distribute, or attempt to reverse-engineer platform internals</li>
                                    </ul>

                                    <p><strong>5. Consent for Body Measurement Processing:</strong>
                                        By registering, the User consents to the following:
                                    </p>
                                    <ul class="list-disc pl-6 space-y-2">
                                        <li>Their submitted measurements will be used for automated gown/suit
                                            recommendations</li>
                                        <li>Shops they engage with may view relevant measurement data for inquiries or
                                            rentals</li>
                                    </ul>

                                    <p><strong>6. Relationship:</strong>
                                        This Agreement does not create an employer/employee, agency, or partnership
                                        relationship
                                        between the User and DressUp Davao.
                                    </p>

                                    <p><strong>7. Continuation of Confidentiality:</strong>
                                        Confidentiality obligations continue even after the User deletes their account,
                                        except for
                                        information the User has already made public.
                                    </p>

                                    <p><strong>8. Legal Disclosures:</strong>
                                        If DressUp Davao is legally required to disclose information, the User will be
                                        informed whenever possible.
                                    </p>
                                </div>

                                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                                    <p class="text-sm text-yellow-800">
                                        <strong>Important:</strong> By checking the box below, you acknowledge that you
                                        have
                                        read,
                                        understood, and agree to this Confidentiality & Data Consent Agreement.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Agreement Checkbox -->
                        <div class="flex items-start space-x-3 p-4 border border-gray-200 rounded-lg bg-white">
                            <input type="checkbox" id="nda_agreement" x-model="form.nda_agreement" required
                                class="mt-1 w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                            <label for="nda_agreement" class="text-sm text-gray-700">
                                <span class="font-medium">I agree to the Confidentiality & Data Consent Agreement
                                    above.</span>
                                <span class="text-red-500 ml-1">*</span>
                                <p class="mt-1 text-gray-600">
                                    I understand that this is a legally binding agreement and that unauthorized
                                    disclosure
                                    or misuse
                                    of platform information may result in account suspension or legal action.
                                </p>
                            </label>
                        </div>
                        <p x-show="errors.nda_agreement" x-text="errors.nda_agreement"
                            class="mt-1 text-sm text-red-600">
                        </p>

                        <!-- Navigation for Agreement Step -->
                        <div class="flex justify-between pt-6">
                            <button type="button" @click="exitForm()"
                                class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50
                                focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                transition-colors duration-200">
                                Disagree & Exit
                            </button>
                            <button type="button" @click="nextStep()" :disabled="!form.nda_agreement"
                                class="px-6 py-3 bg-violet-600 text-white font-medium rounded-md hover:bg-violet-700
                                focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                                transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                I Agree & Continue
                            </button>
                        </div>
                    </div>
                </div>

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
                        <input type="text" name="name" id="name" x-model="form.name" @blur="validateField('name')"
                            required autocomplete="name"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors duration-200"
                            :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.name }"
                            placeholder="Enter your full name" />
                        <p x-show="errors.name" x-text="errors.name" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select name="gender" id="gender" x-model="form.gender" @blur="validateField('gender')" required
                            autocomplete="gender"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors duration-200"
                            :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.gender }">
                            <option value="">Select your gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                        <p x-show="errors.gender" x-text="errors.gender" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone_number" id="phone_number" x-model="form.phone_number"
                            @input="formatPhoneNumber" @blur="validateField('phone_number')" required autocomplete="tel"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors duration-200"
                            :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.phone_number }"
                            placeholder="e.g., 09123456789" />
                        <p x-show="errors.phone_number" x-text="errors.phone_number" class="mt-1 text-sm text-red-600">
                        </p>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" x-model="form.email"
                            @input.debounce.500ms="checkEmailUnique" @blur="validateField('email')" required
                            autocomplete="off"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors duration-200"
                            :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.email || emailStatus === 'taken' }"
                            placeholder="Enter your email address" />
                        <p x-show="emailStatus === 'checking'" class="mt-1 text-sm text-blue-600">Checking
                            availability...</p>
                        <p x-show="emailStatus === 'taken'" class="mt-1 text-sm text-red-600">Email already in use.
                        </p>
                        <p x-show="emailStatus === 'available'" class="mt-1 text-sm text-green-600">Email is
                            available!</p>
                        <p x-show="errors.email" x-text="errors.email" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" name="password" id="password"
                                x-model="form.password" @input="checkPasswordStrength" @blur="validateField('password')"
                                required autocomplete="new-password"
                                class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                    focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors duration-200"
                                :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.password }"
                                placeholder="Enter your password" />
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <template x-if="!showPassword">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                        c4.478 0 8.268 2.943 9.542 7
                                                        -1.274 4.057-5.064 7-9.542 7
                                                        -4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </template>
                                <template x-if="showPassword">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19
                                                        c-4.478 0-8.268-2.943-9.543-7
                                                        a9.97 9.97 0 011.563-3.029m5.858.908
                                                        a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242
                                                        M9.878 9.878L3 3m6.878 6.878L21 21" />
                                    </svg>
                                </template>
                            </button>
                        </div>

                        <!-- Password Requirements -->
                        <div class="mt-2 text-xs text-gray-600 space-y-1">
                            <p class="flex items-center"
                                :class="form.password.length >= 8 ? 'text-green-600' : 'text-gray-500'">
                                <span x-text="form.password.length >= 8 ? '✓' : '•'" class="mr-1"></span>
                                At least 8 characters
                            </p>
                            <p class="flex items-center"
                                :class="/[A-Z]/.test(form.password) ? 'text-green-600' : 'text-gray-500'">
                                <span x-text="/[A-Z]/.test(form.password) ? '✓' : '•'" class="mr-1"></span>
                                At least one uppercase letter
                            </p>
                            <p class="flex items-center"
                                :class="/[^A-Za-z0-9]/.test(form.password) ? 'text-green-600' : 'text-gray-500'">
                                <span x-text="/[^A-Za-z0-9]/.test(form.password) ? '✓' : '•'" class="mr-1"></span>
                                At least one special character (!@#$%^&* etc.)
                            </p>
                        </div>

                        <!-- Password Strength Indicator -->
                        <div class="mt-2">
                            <div class="w-full h-2 rounded-full bg-gray-200">
                                <div class="h-2 rounded-full transition-all duration-300"
                                    :class="passwordStrength.class" :style="{ width: passwordStrength.percent + '%' }">
                                </div>
                            </div>
                            <p class="text-sm mt-1" :class="passwordStrength.textClass" x-text="passwordStrength.label">
                            </p>
                        </div>
                        <p x-show="errors.password" x-text="errors.password" class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showConfirmPassword ? 'text' : 'password'"
                                x-model="form.password_confirmation" @blur="validateField('password_confirmation')"
                                required autocomplete="new-password"
                                class="block w-full px-3 py-2 pr-10 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                                    focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors duration-200"
                                placeholder="Confirm your password" />
                            <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <template x-if="!showConfirmPassword">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                        c4.478 0 8.268 2.943 9.542 7
                                                        -1.274 4.057-5.064 7-9.542 7
                                                        -4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </template>
                                <template x-if="showConfirmPassword">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19
                                                        c-4.478 0-8.268-2.943-9.543-7
                                                        a9.97 9.97 0 011.563-3.029m5.858.908
                                                        a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242
                                                        M9.878 9.878L3 3m6.878 6.878L21 21" />
                                    </svg>
                                </template>
                            </button>
                        </div>
                        <p x-show="errors.password_confirmation" x-text="errors.password_confirmation"
                            class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <div class="flex justify-between pt-6">
                        <button type="button" @click="prevStep()" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                       transition-colors duration-200">
                            Back
                        </button>
                        <button type="button" @click="nextStep()" :disabled="!canProceedToStep2()"
                            class="px-6 py-3 bg-violet-600 text-white font-medium rounded-md hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            Continue
                        </button>
                    </div>
                </div>

                <!-- Step 2: Preferences -->
                <div x-show="currentStep === 2" class="space-y-6">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Style Preferences</h3>
                        <p class="text-sm text-gray-600">Tell us about your style preferences to get better
                            recommendations</p>
                    </div>

                    <!-- Color Preference -->
                    <div>
                        <label for="color_preference" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Color <span class="text-red-500">*</span>
                        </label>
                        <select name="color_preference" id="color_preference" x-model="form.color_preference"
                            @blur="validateField('color_preference')" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                       transition-colors duration-200"
                            :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.color_preference }">
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
                        <p x-show="errors.color_preference" x-text="errors.color_preference"
                            class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Occasion Preference -->
                    <div>
                        <label for="occasion_preference" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Occasion <span class="text-red-500">*</span>
                        </label>
                        <select name="occasion_preference" id="occasion_preference" x-model="form.occasion_preference"
                            @blur="validateField('occasion_preference')" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                       transition-colors duration-200"
                            :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.occasion_preference }">
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
                        <p x-show="errors.occasion_preference" x-text="errors.occasion_preference"
                            class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Fabric Preference -->
                    <div>
                        <label for="fabric_preference" class="block text-sm font-medium text-gray-700 mb-2">
                            Preferred Fabric <span class="text-red-500">*</span>
                        </label>
                        <select name="fabric_preference" id="fabric_preference" x-model="form.fabric_preference"
                            @blur="validateField('fabric_preference')" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                       focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                       transition-colors duration-200"
                            :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.fabric_preference }">
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
                        <p x-show="errors.fabric_preference" x-text="errors.fabric_preference"
                            class="mt-1 text-sm text-red-600"></p>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between pt-6">
                        <button type="button" @click="prevStep()" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                       transition-colors duration-200">
                            Back
                        </button>
                        <button type="button" @click="nextStep()" :disabled="!canProceedToStep3()" class="px-6 py-3 bg-violet-600 text-white font-medium rounded-md hover:bg-violet-700
                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                       disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            Continue
                        </button>
                    </div>
                </div>

                <!-- Step 3: Body Measurements -->
                <div x-show="currentStep === 3" class="space-y-6">
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Body Measurements</h3>
                        <p class="text-sm text-gray-600">Help us recommend the perfect fit (optional)</p>

                        <!-- Unit Toggle -->
                        <div class="flex items-center justify-center space-x-3 mt-4">
                            <span class="text-sm font-medium text-gray-700">Units:</span>
                            <div class="flex bg-gray-100 rounded-lg p-1">
                                <button type="button" @click="measurementUnit = 'inches'" :class="measurementUnit === 'inches' 
                    ? 'bg-white shadow-sm text-violet-700' 
                    : 'text-gray-600 hover:text-gray-900'"
                                    class="px-3 py-1 text-sm font-medium rounded-md transition-all duration-200">
                                    Inches
                                </button>
                                <button type="button" @click="measurementUnit = 'centimeters'" :class="measurementUnit === 'centimeters' 
                    ? 'bg-white shadow-sm text-violet-700' 
                    : 'text-gray-600 hover:text-gray-900'"
                                    class="px-3 py-1 text-sm font-medium rounded-md transition-all duration-200">
                                    Centimeters
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Measurements Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Chest -->
                        <div>
                            <label for="chest" class="block text-sm font-medium text-gray-700 mb-2">
                                Chest (<span x-text="measurementUnit === 'inches' ? 'inches' : 'cm'"></span>)
                            </label>
                            <input type="number" :name="measurementUnit === 'inches' ? 'chest' : 'chest_cm'"
                                :id="measurementUnit === 'inches' ? 'chest' : 'chest_cm'"
                                x-model="measurementUnit === 'inches' ? form.measurements.chest : chestCm"
                                @input="measurementUnit === 'centimeters' ? updateMeasurementFromCm('chest', $event.target.value) : (form.measurements.chest = $event.target.value)"
                                :step="measurementUnit === 'inches' ? '0.5' : '0.1'"
                                :min="measurementUnit === 'inches' ? '20' : '50'"
                                :max="measurementUnit === 'inches' ? '60' : '152'" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                   focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                   transition-colors duration-200"
                                :placeholder="measurementUnit === 'inches' ? 'e.g., 36' : 'e.g., 91.5'" />
                        </div>

                        <!-- Waist -->
                        <div>
                            <label for="waist" class="block text-sm font-medium text-gray-700 mb-2">
                                Waist (<span x-text="measurementUnit === 'inches' ? 'inches' : 'cm'"></span>)
                            </label>
                            <input type="number" :name="measurementUnit === 'inches' ? 'waist' : 'waist_cm'"
                                :id="measurementUnit === 'inches' ? 'waist' : 'waist_cm'"
                                x-model="measurementUnit === 'inches' ? form.measurements.waist : waistCm"
                                @input="measurementUnit === 'centimeters' ? updateMeasurementFromCm('waist', $event.target.value) : (form.measurements.waist = $event.target.value)"
                                :step="measurementUnit === 'inches' ? '0.5' : '0.1'"
                                :min="measurementUnit === 'inches' ? '20' : '50'"
                                :max="measurementUnit === 'inches' ? '50' : '127'" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                   focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                   transition-colors duration-200"
                                :placeholder="measurementUnit === 'inches' ? 'e.g., 32' : 'e.g., 81'" />
                        </div>

                        <!-- Hips -->
                        <div>
                            <label for="hips" class="block text-sm font-medium text-gray-700 mb-2">
                                Hips (<span x-text="measurementUnit === 'inches' ? 'inches' : 'cm'"></span>)
                            </label>
                            <input type="number" :name="measurementUnit === 'inches' ? 'hips' : 'hips_cm'"
                                :id="measurementUnit === 'inches' ? 'hips' : 'hips_cm'"
                                x-model="measurementUnit === 'inches' ? form.measurements.hips : hipsCm"
                                @input="measurementUnit === 'centimeters' ? updateMeasurementFromCm('hips', $event.target.value) : (form.measurements.hips = $event.target.value)"
                                :step="measurementUnit === 'inches' ? '0.5' : '0.1'"
                                :min="measurementUnit === 'inches' ? '20' : '50'"
                                :max="measurementUnit === 'inches' ? '60' : '152'" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                   focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                   transition-colors duration-200"
                                :placeholder="measurementUnit === 'inches' ? 'e.g., 38' : 'e.g., 96.5'" />
                        </div>

                        <!-- Shoulder -->
                        <div>
                            <label for="shoulder" class="block text-sm font-medium text-gray-700 mb-2">
                                Shoulder (<span x-text="measurementUnit === 'inches' ? 'inches' : 'cm'"></span>)
                            </label>
                            <input type="number" :name="measurementUnit === 'inches' ? 'shoulder' : 'shoulder_cm'"
                                :id="measurementUnit === 'inches' ? 'shoulder' : 'shoulder_cm'"
                                x-model="measurementUnit === 'inches' ? form.measurements.shoulder : shoulderCm"
                                @input="measurementUnit === 'centimeters' ? updateMeasurementFromCm('shoulder', $event.target.value) : (form.measurements.shoulder = $event.target.value)"
                                :step="measurementUnit === 'inches' ? '0.5' : '0.1'"
                                :min="measurementUnit === 'inches' ? '10' : '25'"
                                :max="measurementUnit === 'inches' ? '30' : '76'" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400
                   focus:outline-none focus:ring-2 focus:ring-violet-500 focus:border-violet-500
                   transition-colors duration-200"
                                :placeholder="measurementUnit === 'inches' ? 'e.g., 16' : 'e.g., 40.5'" />
                        </div>
                    </div>

                    <!-- Measurement Guide -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Measurement Guide:</h4>
                        <ul class="text-xs text-blue-800 space-y-1">
                            <li><strong>Chest:</strong> Measure around the fullest part of your chest</li>
                            <li><strong>Waist:</strong> Measure around your natural waistline</li>
                            <li><strong>Hips:</strong> Measure around the fullest part of your hips</li>
                            <li><strong>Shoulder:</strong> Measure from shoulder point to shoulder point across your
                                back</li>
                        </ul>
                        <div class="mt-2 text-xs text-blue-700">
                            <strong>Note:</strong> All measurements are stored in inches for consistency.
                        </div>
                    </div>

                    <!-- Navigation -->
                    <div class="flex justify-between pt-6">
                        <button type="button" @click="prevStep()" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-md hover:bg-gray-50
                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500
                   transition-colors duration-200">
                            Back
                        </button>
                        <div class="flex space-x-3">
                            <button type="submit" :disabled="loading" class="px-6 py-3 bg-violet-600 text-white font-medium rounded-md hover:bg-violet-700
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
        <div class="text-center py-6">
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
            currentStep: 0, // Start with Agreement (Step 0)
            measurementUnit: 'inches',
            showAgreement: false, // Controls agreement visibility
            form: {
                nda_agreement: false, // Now in Step 0
                name: '',
                email: '',
                password: '',
                password_confirmation: '',
                gender: '',
                phone_number: '',
                color_preference: '',
                occasion_preference: '',
                fabric_preference: '',
                measurements: { chest: '', waist: '', hips: '', shoulder: '' }
            },
            chestCm: '',
            waistCm: '',
            hipsCm: '',
            shoulderCm: '',
            errors: {},
            showPassword: false,
            showConfirmPassword: false,
            loading: false,
            generalError: '',
            emailStatus: '',
            passwordStrength: { label: '', percent: 0, class: '', textClass: '' },

            // Initialize centimeter values when component loads
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
            },

            // Exit form function - redirects to home
            exitForm() {
                window.location.href = '/';
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
                            this.errors.nda_agreement = 'You must agree to the Non-Disclosure Agreement to continue';
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
                    case 'color_preference':
                        if (!this.form.color_preference) {
                            this.errors.color_preference = 'Color preference is required';
                        }
                        break;
                    case 'occasion_preference':
                        if (!this.form.occasion_preference) {
                            this.errors.occasion_preference = 'Occasion preference is required';
                        }
                        break;
                    case 'fabric_preference':
                        if (!this.form.fabric_preference) {
                            this.errors.fabric_preference = 'Fabric preference is required';
                        }
                        break;
                }
            },

            isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            },

            validateStep0() {
                this.validateField('nda_agreement');
                return !this.errors.nda_agreement;
            },

            validateStep1() {
                this.validateField('name');
                this.validateField('gender');
                this.validateField('phone_number');
                this.validateField('email');
                this.validateField('password');
                this.validateField('password_confirmation');
                return !this.errors.name && !this.errors.gender && !this.errors.phone_number &&
                    !this.errors.email && !this.errors.password && !this.errors.password_confirmation;
            },

            validateStep2() {
                this.validateField('color_preference');
                this.validateField('occasion_preference');
                this.validateField('fabric_preference');
                return !this.errors.color_preference && !this.errors.occasion_preference && !this.errors.fabric_preference;
            },

            canProceedToStep1() {
                // Check if agreement is checked
                return this.form.nda_agreement;
            },

            canProceedToStep2() {
                // Check if all required fields are filled and valid
                const hasName = this.form.name && this.form.name.length >= 2;
                const hasGender = this.form.gender && ['Male', 'Female'].includes(this.form.gender);
                const hasValidPhone = this.form.phone_number &&
                    /^\d+$/.test(this.form.phone_number) &&
                    this.form.phone_number.length >= 10 &&
                    this.form.phone_number.length <= 15;
                const hasValidEmail = this.form.email && this.isValidEmail(this.form.email) && this.emailStatus !== 'taken';
                const hasValidPassword = this.form.password &&
                    this.form.password.length >= 8 &&
                    /[A-Z]/.test(this.form.password) &&
                    /[^A-Za-z0-9]/.test(this.form.password);
                const passwordsMatch = this.form.password === this.form.password_confirmation;

                return hasName && hasGender && hasValidPhone && hasValidEmail && hasValidPassword && passwordsMatch;
            },

            canProceedToStep3() {
                // Check if all preference fields are filled
                const hasColorPreference = this.form.color_preference && this.form.color_preference !== '';
                const hasOccasionPreference = this.form.occasion_preference && this.form.occasion_preference !== '';
                const hasFabricPreference = this.form.fabric_preference && this.form.fabric_preference !== '';

                return hasColorPreference && hasOccasionPreference && hasFabricPreference;
            },

            nextStep() {
                // Validate current step before proceeding
                if (this.currentStep === 0) {
                    if (!this.validateStep0()) {
                        return;
                    }
                } else if (this.currentStep === 1) {
                    if (!this.validateStep1()) {
                        return;
                    }
                    // Additional check for email availability
                    if (this.emailStatus === 'taken') {
                        this.errors.email = 'Email is already in use';
                        return;
                    }
                    if (this.emailStatus === 'checking') {
                        this.errors.email = 'Please wait while we check email availability';
                        return;
                    }
                } else if (this.currentStep === 2) {
                    if (!this.validateStep2()) {
                        return;
                    }
                }

                if (this.currentStep < 3) {
                    this.currentStep++;
                }
            },

            prevStep() {
                if (this.currentStep > 0) {
                    this.currentStep--;
                }
            },

            async submitForm() {
                this.generalError = '';

                // Validate all steps before submission
                if (!this.validateStep0()) {
                    this.currentStep = 0;
                    return;
                }

                if (!this.validateStep1()) {
                    this.currentStep = 1;
                    return;
                }

                if (!this.validateStep2()) {
                    this.currentStep = 2;
                    return;
                }

                // Final email availability check
                if (this.emailStatus === 'taken') {
                    this.errors.email = 'Email is already in use';
                    this.currentStep = 1;
                    return;
                }

                this.loading = true;

                try {
                    const formData = new FormData();
                    formData.append('nda_agreement', this.form.nda_agreement ? '1' : '0');
                    formData.append('name', this.form.name);
                    formData.append('email', this.form.email);
                    formData.append('gender', this.form.gender);
                    formData.append('phone_number', this.form.phone_number);
                    formData.append('password', this.form.password);
                    formData.append('password_confirmation', this.form.password_confirmation);
                    formData.append('color_preference', this.form.color_preference);
                    formData.append('occasion_preference', this.form.occasion_preference);
                    formData.append('fabric_preference', this.form.fabric_preference);

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
                        // Show success toast before redirect
                        if (typeof showToast === 'function') {
                            showToast(data.message || 'Registration successful! Welcome to DressUp Davao!', 'success');
                            // Delay redirect to show toast
                            setTimeout(() => {
                                window.location.href = data.redirect || '/';
                            }, 1500);
                        } else {
                            window.location.href = data.redirect || '/';
                        }
                    } else {
                        if (data.errors) {
                            this.errors = data.errors;
                            // Go back to step 1 if there are validation errors
                            this.currentStep = 1;
                            // Show validation error toast
                            if (typeof showToast === 'function') {
                                showToast('Please check your information and try again.', 'error');
                            }
                        } else {
                            this.generalError = data.message || 'Registration failed. Please try again.';
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
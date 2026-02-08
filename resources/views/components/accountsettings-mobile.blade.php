<!-- accountsettings-mobile.blade.php -->
<div class="w-full max-w-full px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2 flex items-center">
            <svg class="w-6 h-6 mr-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Account Settings
        </h1>
        <p class="text-sm text-gray-600">Manage your account preferences and settings</p>
    </div>

    <!-- Mobile Tabs -->
    <div class="mb-6">
        <div class="flex overflow-x-auto pb-2 space-x-2 -mx-4 px-4">
            <button class="mobile-tab-btn flex-shrink-0 px-4 py-3 rounded-lg font-medium text-sm active" data-tab="profile">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profile
            </button>
            <button class="mobile-tab-btn flex-shrink-0 px-4 py-3 rounded-lg font-medium text-sm" data-tab="password">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Password
            </button>
            <button class="mobile-tab-btn flex-shrink-0 px-4 py-3 rounded-lg font-medium text-sm" data-tab="measurements">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Measurements
            </button>
            <button class="mobile-tab-btn flex-shrink-0 px-4 py-3 rounded-lg font-medium text-sm" data-tab="bookings">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Bookings
            </button>
            <button class="mobile-tab-btn flex-shrink-0 px-4 py-3 rounded-lg font-medium text-sm" data-tab="favorites">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                Favorites
            </button>
            <button class="mobile-tab-btn flex-shrink-0 px-4 py-3 rounded-lg font-medium text-sm" data-tab="preferences">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Preferences
            </button>
            <button class="mobile-tab-btn flex-shrink-0 px-4 py-3 rounded-lg font-medium text-sm" data-tab="delete">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Account
            </button>
        </div>
    </div>

    <!-- Content Sections -->
    <div class="space-y-4">
        <!-- Profile Section -->
        <div id="mobile-profile" class="mobile-content-section active bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                My Profile
            </h2>

            <form method="POST" action="{{ route('profile.update') }}" id="mobileProfileForm">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <div class="flex gap-2">
                            <input type="text" name="name" value="{{ Auth::user()->name }}"
                                class="flex-1 p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                readonly id="mobile-full-name">
                            <button type="button" onclick="mobileEditField('mobile-full-name')"
                                class="px-3 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-all">
                                Edit
                            </button>
                        </div>
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <div class="flex gap-2">
                            <input type="email" name="email" value="{{ maskEmail(Auth::user()->email) }}"
                                class="flex-1 p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                readonly id="mobile-email" data-original="{{ Auth::user()->email }}"
                                data-masked="{{ maskEmail(Auth::user()->email) }}">
                            <button type="button" onclick="mobileEditField('mobile-email')"
                                class="px-3 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-all">
                                Edit
                            </button>
                        </div>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <div class="flex gap-2">
                            <select name="gender" id="mobile-gender"
                                class="flex-1 p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600 appearance-none"
                                disabled>
                                <option value="Male" {{ Auth::user()->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ Auth::user()->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ Auth::user()->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                <option value="Prefer not to say" {{ Auth::user()->gender == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                            </select>
                            <button type="button" onclick="mobileEditField('mobile-gender')"
                                class="px-3 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-all">
                                Edit
                            </button>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <div class="flex gap-2">
                            <input type="tel" name="phone_number" value="{{ maskPhone(Auth::user()->phone_number) }}"
                                class="flex-1 p-3 border border-gray-300 rounded-lg bg-gray-50 text-gray-600"
                                readonly id="mobile-phone" data-original="{{ Auth::user()->phone_number }}"
                                data-masked="{{ maskPhone(Auth::user()->phone_number) }}">
                            <button type="button" onclick="mobileEditField('mobile-phone')"
                                class="px-3 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-all">
                                Edit
                            </button>
                        </div>
                    </div>

                    <!-- Save/Cancel Buttons -->
                    <div id="mobile-form-actions" class="flex gap-2 pt-2 hidden">
                        <button type="button" onclick="mobileCancelEdit()"
                            class="flex-1 px-4 py-3 border-2 border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Change Password Section -->
        <div id="mobile-password" class="mobile-content-section hidden bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Change Password
            </h2>

            <form method="POST" action="{{ route('account.updatePassword') }}">
                @csrf
                <div class="space-y-4">
                    <!-- Current Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <div class="relative">
                            <input type="password" name="current_password" required minlength="8"
                                class="w-full p-3 border border-gray-300 rounded-lg">
                            <button type="button" onclick="mobileTogglePassword(this)"
                                class="absolute right-3 top-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <div class="relative">
                            <input type="password" name="new_password" required minlength="8"
                                class="w-full p-3 border border-gray-300 rounded-lg">
                            <button type="button" onclick="mobileTogglePassword(this)"
                                class="absolute right-3 top-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('new_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                        <div class="relative">
                            <input type="password" name="new_password_confirmation" required minlength="8"
                                class="w-full p-3 border border-gray-300 rounded-lg">
                            <button type="button" onclick="mobileTogglePassword(this)"
                                class="absolute right-3 top-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 mt-2">
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        <!-- Body Measurements Section -->
        <div id="mobile-measurements" class="mobile-content-section hidden bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
                Body Measurements
            </h2>

            <!-- Unit Toggle -->
            <div class="flex items-center justify-center mb-6">
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button type="button" id="mobile-inches-btn" onclick="mobileSwitchToInches()"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all bg-white shadow-sm text-purple-700">
                        Inches
                    </button>
                    <button type="button" id="mobile-cm-btn" onclick="mobileSwitchToCentimeters()"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all text-gray-600">
                        Centimeters
                    </button>
                </div>
            </div>

            @if(Auth::user()->user_measurements()->exists())
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <div class="text-lg font-bold text-purple-600" id="mobile-chest-display">
                            {{ Auth::user()->user_measurements->chest ?? '--' }}
                        </div>
                        <div class="text-xs text-gray-600 mt-1">Chest (in)</div>
                    </div>
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <div class="text-lg font-bold text-purple-600" id="mobile-waist-display">
                            {{ Auth::user()->user_measurements->waist ?? '--' }}
                        </div>
                        <div class="text-xs text-gray-600 mt-1">Waist (in)</div>
                    </div>
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <div class="text-lg font-bold text-purple-600" id="mobile-hips-display">
                            {{ Auth::user()->user_measurements->hips ?? '--' }}
                        </div>
                        <div class="text-xs text-gray-600 mt-1">Hips (in)</div>
                    </div>
                    <div class="text-center bg-gray-50 rounded-lg p-3">
                        <div class="text-lg font-bold text-purple-600" id="mobile-shoulder-display">
                            {{ Auth::user()->user_measurements->shoulder ?? '--' }}
                        </div>
                        <div class="text-xs text-gray-600 mt-1">Shoulder (in)</div>
                    </div>
                </div>
            @else
                <div class="text-center py-4 mb-4">
                    <p class="text-gray-500 mb-3">No measurements recorded yet</p>
                </div>
            @endif

            <!-- Measurements Form -->
            <form method="POST" action="{{ route('measurements.update') }}" class="space-y-4">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Chest (in)</label>
                        <input type="number" name="chest" step="0.5" min="20" max="60"
                            value="{{ Auth::user()->user_measurements->chest ?? '' }}"
                            class="w-full p-3 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Waist (in)</label>
                        <input type="number" name="waist" step="0.5" min="20" max="50"
                            value="{{ Auth::user()->user_measurements->waist ?? '' }}"
                            class="w-full p-3 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hips (in)</label>
                        <input type="number" name="hips" step="0.5" min="20" max="60"
                            value="{{ Auth::user()->user_measurements->hips ?? '' }}"
                            class="w-full p-3 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Shoulder (in)</label>
                        <input type="number" name="shoulder" step="0.5" min="10" max="30"
                            value="{{ Auth::user()->user_measurements->shoulder ?? '' }}"
                            class="w-full p-3 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 mt-4">
                    {{ Auth::user()->user_measurements ? 'Update' : 'Save' }} Measurements
                </button>
            </form>
        </div>

        <!-- Bookings Section -->
        <div id="mobile-bookings" class="mobile-content-section hidden bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Booking History
            </h2>

            @if ($bookings->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-gray-600 mb-4">You don't have any bookings yet.</p>
                    <a href="{{ route('product.list') }}"
                        class="inline-block px-6 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700">
                        Browse Products
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($bookings as $booking)
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $booking->product->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M j, Y') }}</p>
                                </div>
                                @php
                                    $statusColors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800',
                                        'Confirmed' => 'bg-green-100 text-green-800',
                                        'Cancelled' => 'bg-red-100 text-red-800',
                                        'Completed' => 'bg-gray-100 text-gray-800',
                                        'On Going' => 'bg-blue-100 text-blue-800',
                                    ];
                                    $badgeClass = $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                    {{ $booking->status }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 mb-1">{{ $booking->product->user->shop->shop_name ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-600 mb-3">{{ $booking->product->user->shop->shop_address ?? 'N/A' }}</p>
                            
                            @if ($booking->status === 'Completed')
                                @php
                                    $existingReview = \App\Models\ShopReviews::where('user_id', auth()->id())
                                        ->where('shop_id', $booking->product->user->shop->shop_id)
                                        ->first();
                                @endphp

                                @if (!$existingReview)
                                    <button onclick="mobileOpenReviewModal('{{ $booking->product->user->shop->shop_id }}', '{{ $booking->product->user->shop->shop_name }}', false)"
                                        class="w-full py-2 bg-purple-600 text-white text-sm rounded-lg font-medium hover:bg-purple-700">
                                        Leave Review
                                    </button>
                                @else
                                    <button onclick="mobileOpenReviewModal('{{ $booking->product->user->shop->shop_id }}', '{{ $booking->product->user->shop->shop_name }}', true)"
                                        class="w-full py-2 bg-gray-200 text-gray-700 text-sm rounded-lg font-medium hover:bg-gray-300">
                                        Update Review
                                    </button>
                                @endif
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Favorites Section -->
        <div id="mobile-favorites" class="mobile-content-section hidden bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                My Favorites
            </h2>

            @if ($favorites->isEmpty())
                <div class="text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <p class="text-gray-600 mb-4">You haven't added any products to your favorites yet.</p>
                    <a href="{{ route('product.list') }}"
                        class="inline-block px-6 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700">
                        Browse Products
                    </a>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($favorites as $product)
                        @php
                            $mainImage = $product->product_images->first();
                            $thumbnail = null;
                            if ($mainImage) {
                                if ($mainImage->thumbnail_image) {
                                    $thumbnail = $mainImage->thumbnail_image;
                                } elseif ($mainImage->images) {
                                    $imagesArray = is_array($mainImage->images)
                                        ? $mainImage->images
                                        : json_decode($mainImage->images, true);
                                    $thumbnail = $imagesArray[0] ?? null;
                                }
                            }
                        @endphp

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="flex">
                                @if ($thumbnail)
                                    <img src="{{ asset('uploads/' . $thumbnail) }}" alt="{{ $product->name }}"
                                        class="w-24 h-24 object-cover">
                                @else
                                    <div class="w-24 h-24 bg-gray-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 p-3">
                                    <div class="flex justify-between">
                                        <a href="{{ route('product.overview', $product) }}" class="font-medium text-gray-900 hover:text-purple-600">
                                            {{ $product->name }}
                                        </a>
                                        <form action="{{ route('products.unfavorite', $product) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-purple-600 hover:text-red-600">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">{{ $product->type }} • {{ $product->subtype }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $product->user->shop->shop_name ?? 'No Shop' }}</p>
                                    @php
                                        $statusColors = [
                                            'Available' => 'bg-green-100 text-green-800',
                                            'Rented' => 'bg-yellow-100 text-yellow-800',
                                            'Reserved' => 'bg-blue-100 text-blue-800',
                                            'Unavailable' => 'bg-red-100 text-red-800',
                                        ];
                                        $badgeClass = $statusColors[$product->status] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                        {{ $product->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Preferences Section -->
        <div id="mobile-preferences" class="mobile-content-section hidden bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Preferences
            </h2>

            <form method="POST" action="{{ route('account.updatePreferences') }}" id="mobilePreferencesForm">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Color</label>
                        <select name="color_preference" value="{{ Auth::user()->preferences['color'] ?? '' }}"
                            class="w-full p-3 border border-gray-300 rounded-lg">
                            <option value="">Choose a color</option>
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

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Occasion</label>
                        <select name="occasion_preference" value="{{ Auth::user()->preferences['occasion'] ?? '' }}"
                            class="w-full p-3 border border-gray-300 rounded-lg">
                            <option value="">Choose an occasion</option>
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

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Fabric</label>
                        <select name="fabric_preference" value="{{ Auth::user()->preferences['fabric'] ?? '' }}"
                            class="w-full p-3 border border-gray-300 rounded-lg">
                            <option value="">Choose a fabric</option>
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

                    <button type="submit"
                        class="w-full py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700">
                        Update Preferences
                    </button>
                </div>
            </form>
        </div>

        <!-- Delete Account Section -->
        <div id="mobile-delete" class="mobile-content-section hidden bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Delete Account
            </h2>

            @if(Auth::user()->deletion_requested_at)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <p class="font-bold text-yellow-800 text-sm">Account Deletion Scheduled</p>
                            <p class="text-yellow-700 text-sm mt-1">
                                Your account is scheduled for deletion on
                                <strong>{{ Auth::user()->scheduled_deletion_at->format('F j, Y') }}</strong>.
                            </p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('account.cancel-deletion') }}" class="mt-3">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full py-2 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700">
                            Cancel Deletion Request
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-600 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                        <div>
                            <p class="font-bold text-red-800 text-sm">Warning:</p>
                            <p class="text-red-700 text-sm">Deleting your account will remove all your data after 30 days.</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('account.request-deletion') }}" id="mobileDeleteForm">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Type "DELETE" to confirm:
                            </label>
                            <input type="text" name="delete_confirmation" required
                                class="w-full p-3 border border-gray-300 rounded-lg uppercase tracking-wide"
                                oninput="this.value = this.value.toUpperCase()">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Enter your password:
                            </label>
                            <div class="relative">
                                <input type="password" name="delete_password" required
                                    class="w-full p-3 border border-gray-300 rounded-lg">
                                <button type="button" onclick="mobileTogglePassword(this)"
                                    class="absolute right-3 top-3 text-gray-500">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full py-3 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700">
                            Request Account Deletion
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- Review Modal for Mobile -->
<div id="mobile-review-modal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative max-h-[90vh] overflow-y-auto">
        <button onclick="mobileCloseReviewModal()"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 p-1">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <h2 class="text-xl font-bold text-gray-800 mb-2">
            <span id="mobile-modal-action">Leave</span> a Review
        </h2>
        <p id="mobile-modal-shop" class="text-sm text-gray-600 mb-4"></p>

        <form method="POST" action="{{ route('user.submitReview') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="shop_id" id="mobile-modal-shop-id">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                <select name="rating" required
                    class="w-full p-3 border border-gray-300 rounded-lg">
                    <option value="">Select Rating</option>
                    <option value="5">★★★★★ (5) - Excellent</option>
                    <option value="4">★★★★ (4) - Very Good</option>
                    <option value="3">★★★ (3) - Good</option>
                    <option value="2">★★ (2) - Fair</option>
                    <option value="1">★ (1) - Poor</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                <textarea name="comment" rows="3"
                    class="w-full p-3 border border-gray-300 rounded-lg resize-none"
                    placeholder="Share your experience..."></textarea>
            </div>

            <div class="flex gap-2 pt-2">
                <button type="button" onclick="mobileCloseReviewModal()"
                    class="flex-1 px-4 py-2 border-2 border-gray-300 text-gray-700 rounded-lg font-medium">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Mobile Tab Switching
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.mobile-tab-btn');
        const sections = document.querySelectorAll('.mobile-content-section');
        
        // Load active tab from localStorage or default to profile
        const activeTab = localStorage.getItem('mobileActiveTab') || 'profile';
        
        // Set active tab on load
        tabBtns.forEach(btn => {
            if (btn.dataset.tab === activeTab) {
                btn.classList.add('active');
                btn.classList.add('bg-purple-100', 'text-purple-700');
            } else {
                btn.classList.remove('active', 'bg-purple-100', 'text-purple-700');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            }
        });
        
        sections.forEach(section => {
            if (section.id === 'mobile-' + activeTab) {
                section.classList.add('active');
                section.classList.remove('hidden');
            } else {
                section.classList.remove('active');
                section.classList.add('hidden');
            }
        });
        
        // Add click handlers
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const tab = this.dataset.tab;
                
                // Update active tab
                tabBtns.forEach(b => {
                    b.classList.remove('active', 'bg-purple-100', 'text-purple-700');
                    b.classList.add('bg-gray-100', 'text-gray-700');
                });
                this.classList.add('active', 'bg-purple-100', 'text-purple-700');
                this.classList.remove('bg-gray-100', 'text-gray-700');
                
                // Show/hide sections
                sections.forEach(section => {
                    if (section.id === 'mobile-' + tab) {
                        section.classList.add('active');
                        section.classList.remove('hidden');
                    } else {
                        section.classList.remove('active');
                        section.classList.add('hidden');
                    }
                });
                
                // Save to localStorage
                localStorage.setItem('mobileActiveTab', tab);
            });
        });
        
        // Mobile profile form submission
        const profileForm = document.getElementById('mobileProfileForm');
        if (profileForm) {
            profileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // Regular form submission (no AJAX)
                this.submit();
            });
        }
        
        // Mobile preferences form submission
        const preferencesForm = document.getElementById('mobilePreferencesForm');
        if (preferencesForm) {
            preferencesForm.addEventListener('submit', function(e) {
                e.preventDefault();
                // Regular form submission (no AJAX)
                this.submit();
            });
        }
        
        // Mobile delete form submission
        const deleteForm = document.getElementById('mobileDeleteForm');
        if (deleteForm) {
            deleteForm.addEventListener('submit', function(e) {
                const confirmInput = this.querySelector('input[name="delete_confirmation"]');
                if (confirmInput.value !== 'DELETE') {
                    e.preventDefault();
                    alert('Please type exactly "DELETE" to confirm deletion.');
                    confirmInput.focus();
                }
            });
        }
    });
    
    // Mobile Profile Editing Functions
    let mobileOriginalValues = {};
    let mobileCurrentField = null;
    
    function mobileEditField(fieldId) {
        const field = document.getElementById(fieldId);
        const editBtn = field.parentElement.querySelector('button');
        
        mobileCurrentField = fieldId;
        
        // Store original value
        if (field.type === 'email' || field.type === 'tel') {
            mobileOriginalValues[fieldId] = field.getAttribute('data-original') || '';
        } else if (field.tagName === 'SELECT') {
            mobileOriginalValues[fieldId] = field.value;
        } else {
            mobileOriginalValues[fieldId] = field.value;
        }
        
        // Clear field value
        field.value = '';
        
        // Enable field
        if (field.tagName === 'SELECT') {
            field.disabled = false;
        } else {
            field.readOnly = false;
        }
        
        // Change styles
        field.classList.remove('bg-gray-50', 'text-gray-600');
        field.classList.add('bg-white', 'text-gray-900');
        field.focus();
        
        // Show save/cancel buttons
        document.getElementById('mobile-form-actions').classList.remove('hidden');
        
        // Hide all edit buttons in this section
        field.parentElement.parentElement.querySelectorAll('button[onclick^="mobileEditField"]').forEach(btn => {
            btn.style.display = 'none';
        });
    }
    
    function mobileCancelEdit() {
        if (!mobileCurrentField) return;
        
        const field = document.getElementById(mobileCurrentField);
        
        // Restore original value
        if (field.type === 'email' || field.type === 'tel') {
            field.value = field.getAttribute('data-masked');
        } else {
            field.value = mobileOriginalValues[mobileCurrentField] || '';
        }
        
        // Disable field
        if (field.tagName === 'SELECT') {
            field.disabled = true;
        } else {
            field.readOnly = true;
        }
        
        // Reset styles
        field.classList.remove('bg-white', 'text-gray-900');
        field.classList.add('bg-gray-50', 'text-gray-600');
        
        // Hide save/cancel buttons
        document.getElementById('mobile-form-actions').classList.add('hidden');
        
        // Show all edit buttons
        field.parentElement.parentElement.querySelectorAll('button[onclick^="mobileEditField"]').forEach(btn => {
            btn.style.display = 'block';
        });
        
        mobileCurrentField = null;
        mobileOriginalValues = {};
    }
    
    function mobileTogglePassword(button) {
        const input = button.parentElement.querySelector('input');
        if (input.type === 'password') {
            input.type = 'text';
            button.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
            </svg>`;
        } else {
            input.type = 'password';
            button.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>`;
        }
    }
    
    function mobileSwitchToInches() {
        document.getElementById('mobile-inches-btn').classList.add('bg-white', 'text-purple-700');
        document.getElementById('mobile-inches-btn').classList.remove('text-gray-600');
        document.getElementById('mobile-cm-btn').classList.remove('bg-white', 'text-purple-700');
        document.getElementById('mobile-cm-btn').classList.add('text-gray-600');
        
        // Convert all displayed measurements to inches
        const measurements = @json(Auth::user()->user_measurements);
        if (measurements) {
            document.getElementById('mobile-chest-display').textContent = measurements.chest || '--';
            document.getElementById('mobile-waist-display').textContent = measurements.waist || '--';
            document.getElementById('mobile-hips-display').textContent = measurements.hips || '--';
            document.getElementById('mobile-shoulder-display').textContent = measurements.shoulder || '--';
        }
    }
    
    function mobileSwitchToCentimeters() {
        document.getElementById('mobile-cm-btn').classList.add('bg-white', 'text-purple-700');
        document.getElementById('mobile-cm-btn').classList.remove('text-gray-600');
        document.getElementById('mobile-inches-btn').classList.remove('bg-white', 'text-purple-700');
        document.getElementById('mobile-inches-btn').classList.add('text-gray-600');
        
        // Convert all displayed measurements to cm
        const measurements = @json(Auth::user()->user_measurements);
        if (measurements) {
            document.getElementById('mobile-chest-display').textContent = (measurements.chest * 2.54).toFixed(1) || '--';
            document.getElementById('mobile-waist-display').textContent = (measurements.waist * 2.54).toFixed(1) || '--';
            document.getElementById('mobile-hips-display').textContent = (measurements.hips * 2.54).toFixed(1) || '--';
            document.getElementById('mobile-shoulder-display').textContent = (measurements.shoulder * 2.54).toFixed(1) || '--';
        }
    }
    
    function mobileOpenReviewModal(shopId, shopName, isUpdate = false) {
        document.getElementById('mobile-modal-shop-id').value = shopId;
        document.getElementById('mobile-modal-shop').textContent = shopName;
        document.getElementById('mobile-modal-action').textContent = isUpdate ? 'Update' : 'Leave';
        
        document.getElementById('mobile-review-modal').classList.remove('hidden');
        document.getElementById('mobile-review-modal').classList.add('flex');
    }
    
    function mobileCloseReviewModal() {
        document.getElementById('mobile-review-modal').classList.remove('flex');
        document.getElementById('mobile-review-modal').classList.add('hidden');
    }
    
    // Masking functions
    function maskEmail(email) {
        if (!email) return '';
        const [localPart, domain] = email.split('@');
        if (!localPart || !domain) return email;
        
        if (localPart.length <= 2) {
            return localPart.charAt(0) + '*'.repeat(localPart.length - 1) + '@' + domain;
        } else {
            const firstChar = localPart.charAt(0);
            const lastChar = localPart.charAt(localPart.length - 1);
            return firstChar + '*'.repeat(localPart.length - 2) + lastChar + '@' + domain;
        }
    }
    
    function maskPhone(phone) {
        if (!phone) return '';
        const visibleDigits = 4;
        const phoneLength = phone.length;
        if (phoneLength <= visibleDigits) {
            return '*'.repeat(phoneLength);
        }
        const maskedPart = '*'.repeat(phoneLength - visibleDigits);
        const visiblePart = phone.slice(-visibleDigits);
        return maskedPart + visiblePart;
    }
</script>

<style>
    .mobile-tab-btn.active {
        background-color: #f5f3ff;
        color: #7c3aed;
        font-weight: 600;
    }
    
    .mobile-content-section {
        display: none;
    }
    
    .mobile-content-section.active {
        display: block;
    }
    
    /* Hide scrollbar but keep functionality */
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }
    
    .overflow-x-auto::-webkit-scrollbar {
        display: none;
    }
    
    /* Better touch targets */
    button, a, input, select, textarea {
        min-height: 44px;
    }
    
    /* Smooth transitions */
    .transition-all {
        transition: all 0.2s ease-in-out;
    }
</style>
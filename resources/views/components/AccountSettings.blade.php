<div class="flex flex-col lg:flex-row text-black pt-[250px] justify-center lg:space-x-12 px-4 sm:px-6 lg:px-8">
    <!-- Sidebar Navigation -->
    <div class="w-full lg:w-1/4 flex flex-col mb-8 lg:mb-0">
        <div>
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">Account Settings</h1>
        </div>

        <div class="pt-8">
            <div class="flex flex-col">
                <a href="#"
                    class="nav-link text-black pb-4 hover:text-purple-700 transition-colors duration-300 ease-in-out active text-lg font-semibold"
                    data-target="profile-settings">
                    Edit Profile
                </a>
                <a href="#"
                    class="nav-link text-black pb-4 hover:text-purple-700 transition-colors duration-300 ease-in-out text-lg font-semibold"
                    data-target="body-measurements">
                    Body Measurements
                </a>
                <a href="#"
                    class="nav-link text-black pb-4 hover:text-purple-700 transition-colors duration-300 ease-in-out text-lg font-semibold"
                    data-target="preferences">
                    Preferences
                </a>
                <a href="#"
                    class="nav-link text-black pb-4 hover:text-purple-700 transition-colors duration-300 ease-in-out text-lg font-semibold"
                    data-target="my-bookings">
                    My Bookings
                </a>
                <a href="#"
                    class="nav-link text-black pb-4 hover:text-purple-700 transition-colors duration-300 ease-in-out text-lg font-semibold"
                    data-target="my-orders">
                    My Orders
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="w-full lg:w-3/4 bg-white p-8 rounded-lg shadow-md">
        <!-- Profile Settings -->
        <div id="profile-settings" class="content-section active">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">My Profile</h1>
            <p class="text-gray-600 pb-8">Manage and protect your account information.</p>

            <!-- Edit Profile -->
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Left Side -->
                    <div class="space-y-6">
                        <div>
                            <label for="full-name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="full-name" name="name" value="{{ Auth::user()->name }}"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" name="email" value="{{ Auth::user()->email }}"
                                class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                        </div>
                    </div>

                    <!-- Right Side -->
                    <div class="flex flex-col items-center">
                        <img src="{{ asset('frontend-images/gown-category.jpg') }}" alt="Profile Picture"
                            class="h-40 w-40 rounded-full object-cover shadow-lg border-4 border-white ring-2 ring-gray-200">
                        <button type="button"
                            class="mt-6 px-6 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200">
                            Select Image
                        </button>
                    </div>
                </div>

                <!-- Update Profile -->
                <div class="pt-8 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-300">
                        Update Profile
                    </button>
                </div>
            </form>
            <!-- Change Password -->
            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-gray-800 pb-2">Change Password</h2>
                <p class="text-gray-600 pb-6">Update your account password.</p>
                <div class="grid grid-cols-1 gap-y-6">
                    <div>
                        <label for="current-password" class="block text-sm font-medium text-gray-700">Current
                            Password</label>
                        <input type="password" id="current-password"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="new-password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" id="new-password"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="confirm-password" class="block text-sm font-medium text-gray-700">Confirm New
                            Password</label>
                        <input type="password" id="confirm-password"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                    </div>
                </div>
                <div class="pt-8 flex justify-end">
                    <button
                        class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-300">
                        Update Password
                    </button>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="mt-12">
                <h2 class="text-2xl font-semibold text-gray-800 pb-2">Delete Account</h2>
                <p class="text-gray-600 pb-6">Permanently delete your account. This action cannot be undone.</p>
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-md mb-6">
                    <p class="font-bold">Warning:</p>
                    <p>Deleting your account will remove all your data and preferences.</p>
                </div>
                <div class="flex flex-col space-y-4">
                    <div>
                        <label for="delete-confirmation" class="block text-sm font-medium text-gray-700">Type "DELETE"
                            to confirm:</label>
                        <input type="text" id="delete-confirmation"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="delete-password" class="block text-sm font-medium text-gray-700">Enter your password
                            to confirm:</label>
                        <input type="password" id="delete-password"
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                    </div>
                </div>
                <div class="pt-8 flex justify-end">
                    <button
                        class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors duration-300">
                        Delete My Account
                    </button>
                </div>
            </div>
        </div>

        <div id="body-measurements" class="content-section hidden" x-data="measurementsForm()">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">Body Measurements</h1>
            <p class="text-gray-600 pb-8">Your measurements help us recommend products that fit you perfectly.</p>

            <!-- Current Measurements -->
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Current Measurements</h2>
                @if(Auth::user()->user_measurements()->exists())
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ Auth::user()->user_measurements->chest ?? '--' }}
                            </div>
                            <div class="text-sm text-gray-600">Chest (inches)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ Auth::user()->user_measurements->waist ?? '--' }}
                            </div>
                            <div class="text-sm text-gray-600">Waist (inches)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ Auth::user()->user_measurements->hips ?? '--' }}
                            </div>
                            <div class="text-sm text-gray-600">Hips (inches)</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ Auth::user()->user_measurements->shoulder ?? '--' }}
                            </div>
                            <div class="text-sm text-gray-600">Shoulder (inches)</div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500 mb-4">No measurements recorded yet</div>
                        <button type="button" @click="showForm = true"
                            class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                            Add Measurements
                        </button>
                    </div>
                @endif

                @if(Auth::user()->user_measurements)
                    <div class="mt-6 text-center">
                        <button type="button" @click="showForm = true"
                            class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition">
                            Update Measurements
                        </button>
                    </div>
                @endif
            </div>

            <!-- Measurements Form -->
            <div x-show="showForm" class="bg-white border border-gray-200 rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ Auth::user()->user_measurements ? 'Update' : 'Add' }} Measurements
                    </h3>
                    <button type="button" @click="showForm = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="submitMeasurements" class="space-y-6">
                    @csrf

                    <!-- Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Chest -->
                        <div>
                            <label for="chest" class="block text-sm font-medium text-gray-700 mb-2">
                                Chest (inches) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="chest" x-model.number="form.chest" @blur="validateField('chest')"
                                step="0.5" min="20" max="60" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                  focus:ring-purple-500 focus:border-purple-500"
                                :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.chest }"
                                placeholder="e.g., 36">
                            <p x-show="errors.chest" x-text="errors.chest" class="mt-1 text-sm text-red-600"></p>
                        </div>

                        <!-- Waist -->
                        <div>
                            <label for="waist" class="block text-sm font-medium text-gray-700 mb-2">
                                Waist (inches) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="waist" x-model.number="form.waist" @blur="validateField('waist')"
                                step="0.5" min="20" max="50" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                  focus:ring-purple-500 focus:border-purple-500"
                                :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.waist }"
                                placeholder="e.g., 32">
                            <p x-show="errors.waist" x-text="errors.waist" class="mt-1 text-sm text-red-600"></p>
                        </div>

                        <!-- Hips -->
                        <div>
                            <label for="hips" class="block text-sm font-medium text-gray-700 mb-2">
                                Hips (inches) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="hips" x-model.number="form.hips" @blur="validateField('hips')"
                                step="0.5" min="20" max="60" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                  focus:ring-purple-500 focus:border-purple-500"
                                :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.hips }"
                                placeholder="e.g., 38">
                            <p x-show="errors.hips" x-text="errors.hips" class="mt-1 text-sm text-red-600"></p>
                        </div>

                        <!-- Shoulder -->
                        <div>
                            <label for="shoulder" class="block text-sm font-medium text-gray-700 mb-2">
                                Shoulder (inches) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="shoulder" x-model.number="form.shoulder"
                                @blur="validateField('shoulder')" step="0.5" min="10" max="30" required class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm
                                  focus:ring-purple-500 focus:border-purple-500"
                                :class="{ 'border-red-300 focus:ring-red-500 focus:border-red-500': errors.shoulder }"
                                placeholder="e.g., 16">
                            <p x-show="errors.shoulder" x-text="errors.shoulder" class="mt-1 text-sm text-red-600"></p>
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
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4">
                        <button type="button" @click="showForm = false"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="loading"
                            class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 disabled:opacity-50">
                            <span x-show="!loading">{{ Auth::user()->user_measurements ? 'Update' : 'Save' }}
                                Measurements</span>
                            <span x-show="loading" class="flex items-center">Saving...</span>
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
                                <h3 class="text-sm font-medium text-red-800">Error saving measurements</h3>
                                <p class="mt-1 text-sm text-red-700" x-text="generalError"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div x-show="successMessage" class="rounded-md bg-green-50 p-4 border border-green-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800">Success!</h3>
                                <p class="mt-1 text-sm text-green-700" x-text="successMessage"></p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>


        <!-- Preferences -->
        <div id="preferences" class="content-section hidden">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">Preferences</h1>
            <p class="text-gray-600 pb-8">Set your clothing and styling preferences.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label for="preferred-color" class="block text-sm font-medium text-gray-700">Preferred
                        Colors</label>
                    <input type="text" id="preferred-color" placeholder="e.g., Black, Blue, Red"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
                <div>
                    <label for="occasions" class="block text-sm font-medium text-gray-700">Occasions</label>
                    <input type="text" id="occasions" placeholder="e.g., Formal, Casual, Party"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
                <div>
                    <label for="fabric" class="block text-sm font-medium text-gray-700">Preferred Fabric</label>
                    <input type="text" id="fabric" placeholder="e.g., Cotton, Silk, Linen"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
                <div>
                    <label for="style" class="block text-sm font-medium text-gray-700">Preferred Style</label>
                    <input type="text" id="style" placeholder="e.g., Modern, Vintage, Minimalist"
                        class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                </div>
            </div>

            <!-- Update Button -->
            <div class="pt-8 flex justify-end">
                <button
                    class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-300">
                    Update Preferences
                </button>
            </div>
        </div>
        <div id="my-bookings" class="content-section hidden">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">My Bookings</h1>
            <p class="text-gray-600 pb-8">View and manage your upcoming and past bookings.</p>
            <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                        <tr>
                            <th scope="col" class="py-3 px-6">Booking Date</th>
                            <th scope="col" class="py-3 px-6">Item</th>
                            <th scope="col" class="py-3 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">August 25, 2025</td>
                            <td class="py-4 px-6 text-gray-900">Elegant Black Gown</td>
                            <td class="py-4 px-6">
                                <span
                                    class="bg-green-100 text-green-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-green-200 dark:text-green-900">Confirmed</span>
                            </td>
                        </tr>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">September 1, 2025</td>
                            <td class="py-4 px-6 text-gray-900">Classic Navy Suit</td>
                            <td class="py-4 px-6">
                                <span
                                    class="bg-yellow-100 text-yellow-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-yellow-200 dark:text-yellow-900">Pending</span>
                            </td>
                        </tr>
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="py-4 px-6 font-medium text-gray-900 whitespace-nowrap">August 10, 2025</td>
                            <td class="py-4 px-6 text-gray-900">Red Evening Dress</td>
                            <td class="py-4 px-6">
                                <span
                                    class="bg-gray-100 text-gray-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded-full dark:bg-gray-200 dark:text-gray-900">Completed</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="my-orders" class="content-section hidden">
            <h1 class="text-3xl font-semibold text-gray-800 pb-2">My Orders</h1>
            <p class="text-gray-600 pb-8">View your past and current orders.</p>
            <div class="border p-4 text-gray-500">No orders found.</div>
        </div>
    </div>
</div>

<script>
    function measurementsForm() {
        return {
            showForm: false,
            form: {
                chest: Number('{{ Auth::user()->user_measurements->chest ?? 0 }}') || '',
                waist: Number('{{ Auth::user()->user_measurements->waist ?? 0 }}') || '',
                hips: Number('{{ Auth::user()->user_measurements->hips ?? 0 }}') || '',
                shoulder: Number('{{ Auth::user()->user_measurements->shoulder ?? 0 }}') || ''
            },
            errors: {},
            generalError: '',
            successMessage: '',
            loading: false,

            validateField(field) {
                let value = this.form[field];
                if (value === '' || value === null) {
                    this.errors[field] = `${field.charAt(0).toUpperCase() + field.slice(1)} measurement is required`;
                    return;
                }
                switch (field) {
                    case 'chest':
                        if (value < 20 || value > 60) this.errors.chest = 'Chest must be between 20–60';
                        else this.errors.chest = '';
                        break;
                    case 'waist':
                        if (value < 20 || value > 50) this.errors.waist = 'Waist must be between 20–50';
                        else this.errors.waist = '';
                        break;
                    case 'hips':
                        if (value < 20 || value > 60) this.errors.hips = 'Hips must be between 20–60';
                        else this.errors.hips = '';
                        break;
                    case 'shoulder':
                        if (value < 10 || value > 30) this.errors.shoulder = 'Shoulder must be between 10–30';
                        else this.errors.shoulder = '';
                        break;
                }
            },

            validateForm() {
                this.validateField('chest');
                this.validateField('waist');
                this.validateField('hips');
                this.validateField('shoulder');
                return Object.values(this.errors).every(err => !err);
            },

            async submitMeasurements() {
                if (!this.validateForm()) return;

                this.loading = true;
                try {
                    const response = await fetch('/user/measurements', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(this.form)
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || 'Failed to save');

                    this.successMessage = 'Measurements saved successfully!';
                } catch (err) {
                    this.generalError = err.message;
                } finally {
                    this.loading = false;
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // --- Tab / Section Switching Logic ---
        const navLinks = document.querySelectorAll('.nav-link');
        const contentSections = document.querySelectorAll('.content-section');

        navLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault(); // Prevent default link behavior (page reload)

                // Remove 'active' class from all nav links and add to clicked one
                navLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');

                // Hide all content sections
                contentSections.forEach(section => section.classList.add('hidden'));

                // Show the target content section
                const targetId = link.dataset.target; // Get the data-target attribute value
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }
            });
        });

        // Trigger click on the initially active link to display its content
        document.querySelector('.nav-link.active')?.click();
    });
</script>

<style>
    /* Add this to your CSS for the active link style */
    .nav-link.active {
        color: #8b5cf6;
        /* A slightly darker purple for active state */
        font-weight: 600;
        /* Make it semi-bold */
    }
</style>
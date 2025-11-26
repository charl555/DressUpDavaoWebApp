<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Contact Us</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white h-full">
    <main>
        <x-navbar />
        <div class="py-40 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Title Section -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4" style="font-family: 'Playfair Display', serif;">
                        Contact Support
                    </h2>
                    <p class="text-2xl text-gray-700 max-w-3xl mx-auto mb-6 leading-relaxed">
                        Have questions or need assistance?
                    </p>
                    <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                        We're here to help.
                    </p>
                </div>

                <div class="grid gap-12 lg:grid-cols-2 max-w-6xl mx-auto">
                    <!-- Contact Form -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6"
                            style="font-family: 'Playfair Display', serif;">
                            Send us a Message
                        </h3>
                        <form id="contactForm" class="space-y-6" method="POST" action="{{ route('contact.submit') }}">
                            @csrf

                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                <input type="text" id="name" name="name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all duration-300"
                                    placeholder="Your full name" value="{{ old('name') }}">
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all duration-300"
                                    placeholder="your.email@example.com" value="{{ old('email') }}">
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mobile -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Mobile</label>
                                <input type="tel" id="phone" name="phone" maxlength="11"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all duration-300"
                                    placeholder="09123456789" value="{{ old('phone') }}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                                <p class="text-xs text-gray-500 mt-1">Enter 11-digit mobile number (digits only)</p>
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Message -->
                            <div>
                                <label for="message"
                                    class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                <textarea id="message" name="message" rows="5" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-600 focus:border-transparent transition-all duration-300 resize-none"
                                    placeholder="Tell us how we can help you...">{{ old('message') }}</textarea>
                                @error('message')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="submitContactBtn"
                                class="w-full inline-flex justify-center items-center px-8 py-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 disabled:from-purple-300 disabled:to-indigo-300 disabled:cursor-not-allowed disabled:transform-none disabled:hover:scale-100">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span class="btn-text">Send Message</span>
                                <span
                                    class="loading hidden ml-2 animate-spin border-2 border-white border-t-transparent rounded-full w-5 h-5"></span>
                            </button>
                        </form>

                        <!-- Success Message -->
                        @if(session('success'))
                            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-green-700">{{ session('success') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <!-- Contact Details Card -->
                        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6"
                                style="font-family: 'Playfair Display', serif;">
                                Get in Touch
                            </h3>

                            <!-- Phone -->
                            <div class="flex items-start space-x-4 mb-6 group">
                                <div
                                    class="bg-gradient-to-br from-purple-100 to-indigo-100 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-1">Phone</h4>
                                    <p class="text-gray-600">+63 951 058 0966</p>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="flex items-start space-x-4 mb-6 group">
                                <div
                                    class="bg-gradient-to-br from-purple-100 to-indigo-100 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-1">Email</h4>
                                    <p class="text-gray-600">support@dressupdavao.shop</p>
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="flex items-start space-x-4 mb-6 group">
                                <div
                                    class="bg-gradient-to-br from-purple-100 to-indigo-100 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-1">Address</h4>
                                    <p class="text-gray-600">Davao City, Philippines</p>
                                </div>
                            </div>

                            <!-- Business Hours -->
                            <div class="flex items-start space-x-4 group">
                                <div
                                    class="bg-gradient-to-br from-purple-100 to-indigo-100 w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-1">Business Hours</h4>
                                    <p class="text-gray-600">Mon–Sun, 9:00 AM – 6:00 PM</p>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info Card -->
                        <div
                            class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-6 border border-purple-100">
                            <div class="flex items-start space-x-4">
                                <div
                                    class="bg-white w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900 mb-2">Quick Response</h4>
                                    <p class="text-gray-600 text-sm">
                                        We typically respond to all inquiries within 24 hours. For urgent matters,
                                        please call us directly.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAQ Link Section -->
                <div class="text-center mt-16">
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 max-w-2xl mx-auto">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Quick Answers
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Check our FAQ section for instant answers to common questions about rentals, shop
                            registration, and platform usage.
                        </p>
                        <a href="/faq"
                            class="inline-flex items-center px-6 py-3 bg-white text-purple-600 font-semibold rounded-lg border border-purple-200 shadow-sm hover:bg-purple-50 hover:shadow-md transform hover:scale-105 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Visit FAQ Page
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Custom focus styles */
            input:focus,
            textarea:focus {
                outline: none;
                box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.1);
            }

            /* Smooth transitions for all interactive elements */
            input,
            textarea,
            button {
                transition: all 0.3s ease-in-out;
            }

            /* Hover effects for contact items */
            .group:hover .text-gray-600 {
                color: #6b7280;
            }
        </style>

    </main>
    <x-footer />
    <x-toast />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const contactForm = document.getElementById('contactForm');
            const submitBtn = document.getElementById('submitContactBtn');
            const btnText = submitBtn.querySelector('.btn-text');
            const loadingIcon = submitBtn.querySelector('.loading');
            const cooldownSeconds = 30; // 30 seconds cooldown
            let lastSubmitTime = 0;
            let cooldownInterval;

            // Phone number validation
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function (e) {
                // Remove any non-digit characters and limit to 11 characters
                this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);
            });

            // Update submit button state based on form validity
            function updateSubmitButtonState() {
                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const message = document.getElementById('message').value.trim();
                const phone = document.getElementById('phone').value.trim();

                const isFormValid = name && email && message && validateEmail(email) && (!phone || phone.length === 11);

                if (isFormValid) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('from-purple-300', 'to-indigo-300', 'cursor-not-allowed');
                    submitBtn.classList.add('from-purple-600', 'to-indigo-600', 'hover:from-purple-700', 'hover:to-indigo-700', 'cursor-pointer');
                } else {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('from-purple-300', 'to-indigo-300', 'cursor-not-allowed');
                    submitBtn.classList.remove('from-purple-600', 'to-indigo-600', 'hover:from-purple-700', 'hover:to-indigo-700', 'cursor-pointer');
                }
            }

            // Email validation
            function validateEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }

            // Start cooldown timer
            function startCooldown() {
                let remaining = cooldownSeconds;
                btnText.textContent = `Wait ${remaining}s`;

                cooldownInterval = setInterval(() => {
                    remaining--;
                    btnText.textContent = `Wait ${remaining}s`;

                    if (remaining <= 0) {
                        clearInterval(cooldownInterval);
                        updateSubmitButtonState();
                        btnText.textContent = 'Send Message';
                    }
                }, 1000);
            }

            // Reset form and start cooldown
            function handleSuccess() {
                contactForm.reset();
                lastSubmitTime = Date.now();
                startCooldown();
            }

            // Reset button to normal state
            function resetButtonState() {
                submitBtn.disabled = false;
                loadingIcon.classList.add('hidden');
                updateSubmitButtonState();
                btnText.textContent = 'Send Message';
            }

            // Add event listeners for form validation
            document.getElementById('name').addEventListener('input', updateSubmitButtonState);
            document.getElementById('email').addEventListener('input', updateSubmitButtonState);
            document.getElementById('phone').addEventListener('input', updateSubmitButtonState);
            document.getElementById('message').addEventListener('input', updateSubmitButtonState);

            // Form submission
            contactForm.addEventListener('submit', async function (event) {
                event.preventDefault();

                const now = Date.now();
                const timeSinceLastSubmit = (now - lastSubmitTime) / 1000;

                // Check cooldown
                if (timeSinceLastSubmit < cooldownSeconds) {
                    const remaining = Math.ceil(cooldownSeconds - timeSinceLastSubmit);
                    showToast(`Please wait ${remaining}s before sending another message.`, 'warning');
                    return;
                }

                const name = document.getElementById('name').value.trim();
                const email = document.getElementById('email').value.trim();
                const phone = document.getElementById('phone').value.trim();
                const message = document.getElementById('message').value.trim();

                // Validate phone number if provided
                if (phone && phone.length !== 11) {
                    showToast('Please enter a valid 11-digit mobile number.', 'error');
                    return;
                }

                // Validate email
                if (!validateEmail(email)) {
                    showToast('Please enter a valid email address.', 'error');
                    return;
                }

                // Show loading state
                submitBtn.disabled = true;
                btnText.textContent = 'Sending...';
                loadingIcon.classList.remove('hidden');

                try {
                    const formData = new FormData(contactForm);

                    const response = await fetch('{{ route('contact.submit') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok) {
                        showToast(data.message || 'Your message has been sent successfully! We will get back to you within 24 hours.', 'success', 5000);
                        handleSuccess();
                    } else {
                        // Handle validation errors
                        if (data.errors) {
                            const firstError = Object.values(data.errors)[0][0];
                            throw new Error(firstError);
                        }
                        throw new Error(data.message || 'Failed to send message. Please try again.');
                    }
                } catch (error) {
                    console.error('Error:', error);

                    // Check if the message was actually sent (database success but network error)
                    // In a real scenario, you might want to check this differently
                    // For now, we'll assume it failed and let the user retry
                    if (error.message.includes('Failed to fetch') || error.message.includes('Network Error')) {
                        showToast('Network error. Your message may have been sent. Please check your connection and try again if needed.', 'warning');
                    } else {
                        showToast(error.message || 'Failed to send message. Please try again later.', 'error');
                    }

                    resetButtonState();
                }
            });

            // Initialize button state
            updateSubmitButtonState();
        });
    </script>
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>FAQ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#1f2937" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="DressUp" />
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png" />
</head>

<body class="bg-white h-full">
    <main>
        <x-navbar />

        <div class="py-20 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Title Section -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6" style="font-family: 'Playfair Display', serif;">
                        Frequently Asked Questions
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Find answers to common questions about using DressUp Davao as a shop owner or customer
                    </p>
                </div>

                <!-- FAQ Items -->
                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button
                            class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition-all duration-300">
                            <span class="text-lg font-semibold text-gray-900">How do I start renting my products on the
                                platform?</span>
                            <span class="transform transition-transform duration-300">
                                <i class="fas fa-chevron-down text-purple-600"></i>
                            </span>
                        </button>
                        <div class="faq-content hidden px-6 pb-6">
                            <p class="text-gray-600 leading-relaxed">
                                Simply sign up, complete your shop profile, upload your products, and once approved,
                                your store will be live and visible to customers.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button
                            class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition-all duration-300">
                            <span class="text-lg font-semibold text-gray-900">Is there a fee for creating a shop?</span>
                            <span class="transform transition-transform duration-300">
                                <i class="fas fa-chevron-down text-purple-600"></i>
                            </span>
                        </button>
                        <div class="faq-content hidden px-6 pb-6">
                            <p class="text-gray-600 leading-relaxed">
                                We offer a Free Plan with limited features. For more advanced tools like unlimited
                                product uploads and 3D store features, you can upgrade to our Standard or Premium Plans.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button
                            class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition-all duration-300">
                            <span class="text-lg font-semibold text-gray-900">What kind of products can I rent?</span>
                            <span class="transform transition-transform duration-300">
                                <i class="fas fa-chevron-down text-purple-600"></i>
                            </span>
                        </button>
                        <div class="faq-content hidden px-6 pb-6">
                            <p class="text-gray-600 leading-relaxed">
                                You can rent gowns and suits. Make sure all products
                                follow our content and quality guidelines.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button
                            class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition-all duration-300">
                            <span class="text-lg font-semibold text-gray-900">How do I receive customer inquiries or
                                booking requests?</span>
                            <span class="transform transition-transform duration-300">
                                <i class="fas fa-chevron-down text-purple-600"></i>
                            </span>
                        </button>
                        <div class="faq-content hidden px-6 pb-6">
                            <p class="text-gray-600 leading-relaxed">
                                Customers can message you directly through the built-in chat system. You will also
                                receive booking or rental requests through your dashboard.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button
                            class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition-all duration-300">
                            <span class="text-lg font-semibold text-gray-900">How do I rent an item on the
                                platform?</span>
                            <span class="transform transition-transform duration-300">
                                <i class="fas fa-chevron-down text-purple-600"></i>
                            </span>
                        </button>
                        <div class="faq-content hidden px-6 pb-6">
                            <p class="text-gray-600 leading-relaxed">
                                Just browse the available gowns and suits, select your preferred item, check the size
                                and details, then send a booking request to the shop. They will confirm your rental
                                through the system.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 6 -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button
                            class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition-all duration-300">
                            <span class="text-lg font-semibold text-gray-900">Can customers leave reviews?</span>
                            <span class="transform transition-transform duration-300">
                                <i class="fas fa-chevron-down text-purple-600"></i>
                            </span>
                        </button>
                        <div class="faq-content hidden px-6 pb-6">
                            <p class="text-gray-600 leading-relaxed">
                                Yes. After completing a rental, customers can leave ratings and reviews to help other
                                buyers and support your shop's credibility.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 7 -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button
                            class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition-all duration-300">
                            <span class="text-lg font-semibold text-gray-900">How do I know if an item is available on
                                my event date?</span>
                            <span class="transform transition-transform duration-300">
                                <i class="fas fa-chevron-down text-purple-600"></i>
                            </span>
                        </button>
                        <div class="faq-content hidden px-6 pb-6">
                            <p class="text-gray-600 leading-relaxed">
                                Each product page shows its availability and the date when it will be available again.
                                You may also contact the shop owner directly through the built-in chat to confirm your
                                schedule.
                            </p>
                        </div>
                    </div>

                    <!-- FAQ 8 -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <button
                            class="faq-toggle w-full flex justify-between items-center p-6 text-left hover:bg-gray-50 transition-all duration-300">
                            <span class="text-lg font-semibold text-gray-900">Can I rent multiple items at once?</span>
                            <span class="transform transition-transform duration-300">
                                <i class="fas fa-chevron-down text-purple-600"></i>
                            </span>
                        </button>
                        <div class="faq-content hidden px-6 pb-6">
                            <p class="text-gray-600 leading-relaxed">
                                Yes. You can book multiple items as long as they are available on your event date.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Additional Help Section -->
                <div class="text-center mt-12">
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-8 max-w-2xl mx-auto">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Still have questions?
                        </h3>
                        <p class="text-gray-600 mb-6">
                            Can't find the answer you're looking for? Please reach out to our support team.
                        </p>
                        <a href="/contact"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg shadow-lg hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                            </svg>
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- JavaScript for FAQ functionality -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const faqToggles = document.querySelectorAll('.faq-toggle');

                // Initialize all FAQs as closed
                function initializeFAQs() {
                    document.querySelectorAll('.faq-content').forEach(content => {
                        content.classList.add('hidden');
                    });
                    document.querySelectorAll('.faq-toggle i').forEach(icon => {
                        icon.style.transform = 'rotate(0deg)';
                    });
                }

                // Initialize on page load
                initializeFAQs();

                faqToggles.forEach(toggle => {
                    toggle.addEventListener('click', function () {
                        const content = this.nextElementSibling;
                        const icon = this.querySelector('i');

                        // Close all other FAQs
                        document.querySelectorAll('.faq-content').forEach(otherContent => {
                            if (otherContent !== content) {
                                otherContent.classList.add('hidden');
                            }
                        });

                        // Reset all other icons
                        document.querySelectorAll('.faq-toggle i').forEach(otherIcon => {
                            if (otherIcon !== icon) {
                                otherIcon.style.transform = 'rotate(0deg)';
                            }
                        });

                        // Toggle current FAQ
                        content.classList.toggle('hidden');

                        // Rotate chevron icon
                        if (content.classList.contains('hidden')) {
                            icon.style.transform = 'rotate(0deg)';
                        } else {
                            icon.style.transform = 'rotate(180deg)';
                        }
                    });
                });
            });
        </script>

        <style>
            .faq-toggle {
                transition: all 0.3s ease-in-out;
            }

            .faq-content {
                transition: all 0.3s ease-in-out;
            }

            /* Smooth hover effects */
            .faq-toggle:hover {
                background-color: #f9fafb;
            }

            /* Ensure proper spacing and borders */
            .bg-white {
                border: 1px solid #f3f4f6;
            }

            .bg-white:hover {
                border-color: #e5e7eb;
            }
        </style>

    </main>
    <x-footer />

</body>

</html>
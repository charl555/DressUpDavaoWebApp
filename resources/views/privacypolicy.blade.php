<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Privacy Policy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white h-full">
    <main>
        <x-navbar />

        <div class="py-40 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Title Section -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6" style="font-family: 'Playfair Display', serif;">
                        Privacy Policy
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Your privacy is important to us. This policy explains how we collect, use, and protect your
                        personal information.
                    </p>
                </div>

                <!-- Privacy Policy Content -->
                <div class="space-y-8">
                    <!-- Collection of Personal Information -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Collection of Your Personal Information
                        </h3>
                        <div class="space-y-4 text-gray-600 leading-relaxed">
                            <p>
                                Many sections of the DressUp Davao platform can be accessed without providing personal
                                information. However, to create an account, manage a shop profile, make rental bookings,
                                or use certain features, you are required to submit personally identifiable information.
                            </p>
                            <p>
                                This may include, but is not limited to, your name, email address, contact number,
                                postal address, username, password, and shop details (for sellers).
                            </p>
                        </div>
                    </div>

                    <!-- Sharing of Personal Information -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Sharing of Your Personal Information
                        </h3>
                        <div class="space-y-4 text-gray-600 leading-relaxed">
                            <p>
                                We may occasionally engage third-party companies to perform services on our behalf,
                                including but not limited to customer support, hosting services, payment processing,
                                verification, and system maintenance.
                            </p>
                            <p>
                                These companies will only be permitted to obtain the personal information necessary to
                                perform their assigned duties.
                            </p>
                            <p class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-600">
                                DressUp Davao takes reasonable steps to ensure that all third-party service providers
                                are bound by confidentiality and privacy obligations regarding the protection of your
                                personal information.
                            </p>
                        </div>
                    </div>

                    <!-- Use of Personal Information -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Use of Your Personal Information
                        </h3>
                        <div class="space-y-4 text-gray-600 leading-relaxed">
                            <p>
                                For every visitor accessing the platform, we automatically collect certain
                                non-identifiable information such as browser type, version, language, operating system,
                                pages viewed, time spent on pages, and referring website addresses.
                            </p>
                            <p>This information is used internally to:</p>
                            <ul class="list-disc list-inside space-y-2 ml-4">
                                <li>Analyze visitor traffic</li>
                                <li>Identify usage patterns</li>
                                <li>Improve site performance</li>
                                <li>Deliver more relevant and personalized content</li>
                            </ul>
                            <p>
                                From time to time, we may use customer information for new or updated purposes not
                                previously disclosed in this Privacy Policy. If such changes occur, any new data
                                practices will apply only to information collected after the policy update.
                            </p>
                        </div>
                    </div>

                    <!-- Changes to Privacy Policy -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Changes to This Privacy Policy
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            DressUp Davao reserves the right to amend this Privacy Policy at any time. Updated versions
                            will be posted on this page. If you disagree with the Privacy Policy or any changes made,
                            you should discontinue use of the platform.
                        </p>
                    </div>

                    <!-- Accessing Your Personal Information -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Accessing Your Personal Information
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            You have the right to access your personal information, subject to exceptions allowed by
                            law. If you would like to request access, review, or correction of your data, please contact
                            us. For security reasons, you may be required to submit your request in writing. DressUp
                            Davao reserves the right to charge a fee for locating and providing access to your
                            information on a per-request basis.
                        </p>
                    </div>

                    <!-- Deleting Your Personal Information -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Deleting Your Personal Information
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            You may request deletion of your personal data by contacting our support team. If you make
                            this request, all personally identifiable information will be permanently deleted from our
                            systems within 3 business days. DressUp Davao will retain certain transactional records
                            (e.g., rental history, receipts) only when required for accounting or compliance purposes.
                            These records will no longer be identifiable or linked to you in any way.
                        </p>
                    </div>

                    <!-- Contact Information -->
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-8 border border-purple-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Contacting Us
                        </h3>
                        <p class="text-gray-600 mb-6">
                            DressUp Davao welcomes any questions or comments regarding this Privacy Policy. If you
                            require further information, please contact us through the following:
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 text-gray-700">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                support@dressupdavao.shop
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Davao City, Philippines
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-footer />

</body>

</html>
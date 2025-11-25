<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Terms and Services</title>
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
                        Terms and Services
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        Welcome to DressUp Davao! By using our platform, you agree to the following terms and services.
                    </p>
                </div>

                <!-- Terms Content -->
                <div class="space-y-8">
                    <!-- Account Registration -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Account Registration
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Users must provide accurate information. Shop owners must ensure all uploaded products are
                            real, available, and compliant with platform guidelines.
                        </p>
                    </div>

                    <!-- Platform Use -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Platform Use
                        </h3>
                        <div class="space-y-6">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 text-purple-600">Users may:</h4>
                                <ul class="list-disc list-inside space-y-2 text-gray-600 ml-4">
                                    <li>Browse gowns, suits, and other fashion items</li>
                                    <li>Communicate with shops through chat</li>
                                    <li>Make booking requests</li>
                                    <li>Manage shop Product Listing (for Shop Owners)</li>
                                </ul>
                            </div>
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900 mb-3 text-red-600">Users must NOT:</h4>
                                <ul class="list-disc list-inside space-y-2 text-gray-600 ml-4">
                                    <li>Upload misleading content</li>
                                    <li>Impersonate other individuals</li>
                                    <li>Post inappropriate or copyrighted images</li>
                                    <li>Abuse or spam the chat system</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Shop Responsibilities -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Shop Responsibilities
                        </h3>
                        <p class="text-gray-600 mb-4 leading-relaxed">Shop owners must:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-600 ml-4">
                            <li>Provide accurate details and images</li>
                            <li>Manage availability and pricing</li>
                            <li>Handle rentals, damages, refunds, and late fees</li>
                            <li>Respond professionally to customer inquiries</li>
                        </ul>
                        <div class="mt-4 bg-yellow-50 p-4 rounded-lg border-l-4 border-yellow-500">
                            <p class="text-yellow-800 font-medium">DressUp Davao is NOT responsible for disputes between
                                sellers and customers.</p>
                        </div>
                    </div>

                    <!-- Payment & Fees -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Payment & Fees
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Subscription plans (Free, Standard, Premium) provide different access levels. Fees, when
                            applicable, are non-refundable unless stated otherwise.
                        </p>
                    </div>

                    <!-- Booking & Rental Policies -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Booking & Rental Policies
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            Individual shops determine their own return deadlines, damage fees, cancellation fees, and
                            pickup/delivery arrangements.
                        </p>
                    </div>

                    <!-- Liability Disclaimer -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Liability Disclaimer
                        </h3>
                        <p class="text-gray-600 mb-4 leading-relaxed">We are NOT responsible for:</p>
                        <ul class="list-disc list-inside space-y-2 text-gray-600 ml-4">
                            <li>Damaged or lost rented items</li>
                            <li>Shop-owner misconduct</li>
                            <li>Customer misuse or violations</li>
                            <li>Service interruptions due to technical issues</li>
                        </ul>
                    </div>

                    <!-- Termination -->
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4"
                            style="font-family: 'Playfair Display', serif;">
                            Termination
                        </h3>
                        <p class="text-gray-600 leading-relaxed">
                            We may suspend or delete accounts that violate our policies.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <x-footer />

</body>

</html>
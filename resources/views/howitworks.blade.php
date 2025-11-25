<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>How it works</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white h-full">
    <main>
        <x-navbar />
        <div class="py-40 bg-gradient-to-b from-white to-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Title Section -->
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6" style="font-family: 'Playfair Display', serif;">
                        How It Works
                    </h2>
                    <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                        DressUp Davao makes gown and suit rentals easier for both customers and shops.
                    </p>
                </div>

                <!-- Customer Process -->
                <div class="mb-16">
                    <h3 class="text-3xl font-bold text-gray-900 mb-8 text-center"
                        style="font-family: 'Playfair Display', serif;">
                        For Customers
                    </h3>
                    <div class="grid gap-6 md:grid-cols-3 lg:grid-cols-6">
                        <!-- Step 1 -->
                        <div class="text-center group">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">1</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Browse</h4>
                            <p class="text-sm text-gray-600">gowns and suits from multiple shops</p>
                        </div>

                        <!-- Step 2 -->
                        <div class="text-center group">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">2</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">View Details</h4>
                            <p class="text-sm text-gray-600">including sizes, rental dates, and availability</p>
                        </div>

                        <!-- Step 3 -->
                        <div class="text-center group">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">3</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Chat</h4>
                            <p class="text-sm text-gray-600">with the shop for fitting or inquiries</p>
                        </div>

                        <!-- Step 4 -->
                        <div class="text-center group">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">4</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Send Booking</h4>
                            <p class="text-sm text-gray-600">request for your event date</p>
                        </div>

                        <!-- Step 5 -->
                        <div class="text-center group">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">5</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Pick Up</h4>
                            <p class="text-sm text-gray-600">or receive delivery based on shop policy</p>
                        </div>

                        <!-- Step 6 -->
                        <div class="text-center group">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">6</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Return Item</h4>
                            <p class="text-sm text-gray-600">on the agreed schedule</p>
                        </div>
                    </div>
                </div>

                <!-- Shop Owner Process -->
                <div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-8 text-center"
                        style="font-family: 'Playfair Display', serif;">
                        For Shop Owners
                    </h3>
                    <div class="grid gap-6 md:grid-cols-3">
                        <!-- Step 1 -->
                        <div
                            class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">1</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Create Shop Account</h4>
                        </div>

                        <!-- Step 2 -->
                        <div
                            class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">2</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Upload Products</h4>
                            <p class="text-sm text-gray-600">with descriptions and photos</p>
                        </div>

                        <!-- Step 3 -->
                        <div
                            class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">3</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Set Rental Price & Availability</h4>
                        </div>

                        <!-- Step 4 -->
                        <div
                            class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">4</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Receive Booking Requests</h4>
                        </div>

                        <!-- Step 5 -->
                        <div
                            class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">5</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Manage Inventory & Customers</h4>
                        </div>

                        <!-- Step 6 -->
                        <div
                            class="text-center group bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
                            <div
                                class="bg-gradient-to-br from-purple-100 to-indigo-100 w-16 h-16 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:from-purple-200 group-hover:to-indigo-200 transition-all duration-300">
                                <span class="text-2xl font-bold text-purple-600">6</span>
                            </div>
                            <h4 class="font-semibold text-gray-900 mb-2">Grow Visibility</h4>
                            <p class="text-sm text-gray-600">through the platform's features</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
    <x-footer />

</body>

</html>
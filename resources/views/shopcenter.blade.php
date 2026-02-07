<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>List Your Shop - DressUp Davao</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#1f2937" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="DressUp" />
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png" />
</head>

<body class="bg-white min-h-screen flex flex-col">
    <x-navbar />

    <main class="flex-grow pt-[72px]">
        <!-- Hero Section with Background Image -->
        <section class="relative py-20 text-white bg-gray-900">
            <!-- Background Image Container -->
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat opacity-60"
                style="background-image: url('{{ asset('frontend-images/gown-backdrop-sea.webp') }}');"></div>

            <!-- Dark Overlay -->
            <div class="absolute inset-0 bg-black opacity-50"></div>

            <!-- Content -->
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl mb-6" style="font-family: 'Playfair Display', serif;">
                    Transform Your Rental Business With Digital Efficiency
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-200">Join Davao's premier platform for formal wear rentals
                    and streamline your shop operations</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'SuperAdmin')
                            <a href="/admin"
                                class="bg-white text-gray-900 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors duration-200 shadow-lg">
                                Go to Shop Dashboard
                            </a>
                        @else
                            <a href="/admin/register"
                                class="bg-white text-gray-900 px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors duration-200 shadow-lg">
                                Register Your Shop
                            </a>
                        @endif
                    @else
                        <a href="/admin/register"
                            class="bg-purple-600 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white hover:text-gray-900 transition-colors duration-200 shadow-lg">
                            Register Your Shop
                        </a>
                        <a href="/admin/login"
                            class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-purple-600 hover:text-white hover:border-purple-600 transition-colors duration-200">
                            Shop Login
                        </a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Benefits Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Why Join Our Platform?</h2>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3-3H7" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Enhanced Visibility</h3>
                        <p class="text-gray-600">Showcase your gowns and suits to thousands of customers browsing for
                            rentals in Davao. Increase your shop's reach beyond walk-in customers.</p>
                    </div>

                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Verified Shop System</h3>
                        <p class="text-gray-600">Build trust with customers through our verification process. Only
                            legitimate rental shops are approved, ensuring quality and reliability.</p>
                    </div>

                    <div class="text-center">
                        <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Automated Inventory</h3>
                        <p class="text-gray-600">Smart product status tracking automatically updates availability.
                            Customers see real-time rental status and return dates.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-12"> Shop Management Tools</h2>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Digital Inventory System</h3>
                        <p class="text-gray-600">Manage your entire collection digitally. Track gowns, suits, and
                            accessories with detailed specifications and availability status.</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">3D Product Visualization</h3>
                        <p class="text-gray-600">Upload photos to generate interactive 3D models of your products. Give
                            customers a better view of your gowns and suits before they visit.</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Customer Management</h3>
                        <p class="text-gray-600">Store customer information, rental history, and preferences. Streamline
                            the booking process for repeat customers.</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Inquiry Management</h3>
                        <p class="text-gray-600">Handle customer inquiries efficiently through our messaging system.
                            Convert interest into confirmed rentals seamlessly.</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Automated Availability</h3>
                        <p class="text-gray-600">Automatic status updates prevent double bookings. Customers see exactly
                            when rented items will be available again.</p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Shop Profile Customization</h3>
                        <p class="text-gray-600">Create a professional shop profile with your branding, policies, and
                            contact information to build customer trust.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Simple Setup Process</h2>

                <div class="grid md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div
                            class="bg-purple-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-lg">
                            1</div>
                        <h3 class="text-lg font-semibold mb-2">Apply & Verify</h3>
                        <p class="text-gray-600">Register your shop and complete our verification process to ensure
                            quality standards</p>
                    </div>

                    <div class="text-center">
                        <div
                            class="bg-purple-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-lg">
                            2</div>
                        <h3 class="text-lg font-semibold mb-2">Setup Inventory</h3>
                        <p class="text-gray-600">Add your products with photos, descriptions, and generate 3D models for
                            better visualization</p>
                    </div>

                    <div class="text-center">
                        <div
                            class="bg-purple-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-lg">
                            3</div>
                        <h3 class="text-lg font-semibold mb-2">Receive Inquiries</h3>
                        <p class="text-gray-600">Customers browse your collection and send inquiries through the
                            platform</p>
                    </div>

                    <div class="text-center">
                        <div
                            class="bg-purple-600 text-white w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-lg">
                            4</div>
                        <h3 class="text-lg font-semibold mb-2">Manage Rentals</h3>
                        <p class="text-gray-600">Handle bookings, track inventory, and provide excellent service to grow
                            your business</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <x-bottom-navbar />
    <x-footer />
</body>

</html>
@php
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dress Up Davao</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    @if($isMobileApp)
        <link rel="preload" href="{{ asset('frontend-images/optimized-images/app-hero-mobile.webp') }}" as="image">
    @else
        <link rel="preload" href="{{ asset('frontend-images/optimized-images/hero-mobile.webp') }}" as="image"
            media="(max-width: 768px)">
        <link rel="preload" href="{{ asset('frontend-images/optimized-images/hero-desktop.webp') }}" as="image"
            media="(min-width: 769px)">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#7c3aed" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="DressUp" />
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png" />

    <style>
        @if($isMobileApp)
            body {
                padding-top: 64px !important;
                padding-bottom: 64px !important;
                background-color: #fafafa;
                -webkit-tap-highlight-color: transparent;
            }

            /* Native-like scroll behavior */
            * {
                -webkit-overflow-scrolling: touch;
            }

            /* Better touch targets */
            button,
            a {
                min-height: 44px;
                min-width: 44px;
            }

        @endif

        /* Skeleton loading */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 8px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Pull-to-refresh simulation */
        .refresh-indicator {
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transform: translateY(-100%);
            transition: transform 0.3s ease;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.85));
            backdrop-filter: blur(10px);
        }

        .refresh-indicator.active {
            transform: translateY(0);
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white min-h-screen flex flex-col">
    <main class="flex-grow">
        {{-- Navbar --}}
        <x-navbar />

        {{-- Mobile App Content --}}
        @if($isMobileApp)
            <div class="mobile-app-home">
                {{-- Search Bar --}}
                <div class="px-4 py-3 bg-white border-b border-gray-100">
                    <div class="relative">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" placeholder="Search dresses, suits, shops..."
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                    </div>
                </div>

                {{-- App Hero Banner --}}
                <div class="relative overflow-hidden bg-gradient-to-br from-purple-600 to-indigo-600 px-4 py-8">
                    <div class="relative z-10">
                        <h1 class="text-2xl font-bold text-white mb-2">Welcome to DressUp</h1>
                        <p class="text-purple-100 mb-6">Find your perfect outfit for any occasion</p>
                        <div class="flex space-x-3">
                            <a href="/product-list?app=1&mobile_nav=true"
                                class="flex-1 px-4 py-3 bg-white text-purple-700 font-semibold rounded-xl text-center hover:bg-gray-50 transition-colors duration-200">
                                Browse
                            </a>
                            <a href="/shops?app=1&mobile_nav=true"
                                class="flex-1 px-4 py-3 bg-purple-800 text-white font-semibold rounded-xl text-center hover:bg-purple-900 transition-colors duration-200">
                                Shops
                            </a>
                        </div>
                    </div>

                    <!-- Decorative pattern -->
                    <div class="absolute right-0 top-0 bottom-0 w-32 opacity-20">
                        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=" 100"
                            height="100" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg" %3E%3Cpath
                            d="M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z"
                            fill="%23ffffff" fill-opacity="0.4" fill-rule="evenodd" /%3E%3C/svg%3E'); background-size: 20px
                            20px;"></div>
                    </div>
                </div>

                {{-- Quick Categories --}}
                <div class="px-4 py-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Categories</h2>
                        <a href="/product-list?app=1&mobile_nav=true" class="text-sm text-purple-600 font-medium">View
                            all</a>
                    </div>

                    <div class="grid grid-cols-4 gap-3">
                        <a href="/product-list?type=Gown&app=1&mobile_nav=true"
                            class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">Gowns</span>
                        </a>

                        <a href="/product-list?type=Suit&app=1&mobile_nav=true"
                            class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">Suits</span>
                        </a>

                        <a href="/shops?app=1&mobile_nav=true"
                            class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="w-12 h-12 rounded-full bg-pink-100 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">Shops</span>
                        </a>

                        <a href="/account?app=1&mobile_nav=true"
                            class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">Favorites</span>
                        </a>
                    </div>
                </div>

                {{-- Featured Products --}}
                <div class="px-4 py-6 bg-gray-50">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Featured</h2>
                        <a href="/product-list?app=1&mobile_nav=true" class="text-sm text-purple-600 font-medium">See
                            all</a>
                    </div>

                    <div class="space-y-4">
                        @forelse ($products->take(3) as $product)
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                                <div class="flex">
                                    <div class="w-24 h-24 flex-shrink-0">
                                        @php
                                            $imageRecord = $product->product_images->first();
                                            $imageUrl = $imageRecord && $imageRecord->thumbnail_image
                                                ? asset('uploads/' . $imageRecord->thumbnail_image)
                                                : null;
                                        @endphp

                                        @if ($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-cover">
                                        @else
                                            <div
                                                class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1 p-3">
                                        <h3 class="font-medium text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                                        <div class="flex items-center mb-2">
                                            <span
                                                class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded-full capitalize">
                                                {{ $product->subtype }}
                                            </span>
                                        </div>
                                        <a href="{{ route('product.overview', ['product_id' => $product->product_id]) }}?app=1&mobile_nav=true"
                                            class="inline-flex items-center text-sm text-purple-600 font-medium hover:text-purple-700">
                                            View details
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6" />
                                </svg>
                                <p class="text-gray-500 mb-4">No featured products available</p>
                                <a href="/product-list?app=1&mobile_nav=true"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-colors duration-200">
                                    Browse Collections
                                </a>
                            </div>
                        @endforelse
                    </div>

                    @if($products->count() > 3)
                        <div class="mt-6 text-center">
                            <a href="/product-list?app=1&mobile_nav=true"
                                class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <span>View More Products</span>
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- App Features --}}
                <div class="px-4 py-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 text-center">Why Choose DressUp?</h2>

                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.6l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.6l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Quality Assured</h3>
                                <p class="text-sm text-gray-600">All items are carefully curated and inspected for quality.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Multiple Vendors</h3>
                                <p class="text-sm text-gray-600">Access a wide selection from trusted rental shops in Davao.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-10 h-10 rounded-full bg-pink-100 flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Easy Booking</h3>
                                <p class="text-sm text-gray-600">Simple, fast booking process with flexible rental options.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Original Web Content --}}
            <x-hero />
            <x-infocardscomponent />
            <x-category />
            <x-productcards :products="$products" />
        @endif

        {{-- Bottom navbar for mobile app --}}
        <x-bottom-navbar />
    </main>

    @unless($isMobileApp)
        <x-footer />
        <x-chatwindow />
    @endunless

    <x-toast />

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));

            if ('IntersectionObserver' in window) {
                const lazyImageObserver = new IntersectionObserver(function (entries, observer) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            const lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.classList.remove('lazy');
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function (lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            }

            @if($isMobileApp)
                // Mobile app specific JavaScript
                console.log('Mobile app UI loaded');

                // Add pull-to-refresh simulation
                let startY = 0;
                let pullDistance = 0;
                const refreshIndicator = document.createElement('div');
                refreshIndicator.className = 'refresh-indicator';
                refreshIndicator.innerHTML = `
                                <div class="flex items-center space-x-2">
                                    <svg class="w-6 h-6 text-purple-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span class="text-gray-700 font-medium">Refreshing...</span>
                                </div>
                            `;
                document.body.appendChild(refreshIndicator);

                document.addEventListener('touchstart', function (e) {
                    if (window.scrollY === 0) {
                        startY = e.touches[0].clientY;
                    }
                });

                document.addEventListener('touchmove', function (e) {
                    if (startY && window.scrollY === 0) {
                        pullDistance = e.touches[0].clientY - startY;
                        if (pullDistance > 0) {
                            e.preventDefault();
                            refreshIndicator.style.transform = `translateY(${Math.min(pullDistance, 60)}px)`;
                        }
                    }
                });

                document.addEventListener('touchend', function () {
                    if (pullDistance > 100) {
                        refreshIndicator.classList.add('active');
                        // Simulate refresh
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                    refreshIndicator.style.transform = 'translateY(-100%)';
                    startY = 0;
                    pullDistance = 0;
                });
            @endif
        });
    </script>
</body>

</html>
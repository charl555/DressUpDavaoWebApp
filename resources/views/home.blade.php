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
        <link rel="preload" href="{{ asset('frontend-images/optimized-images/hero-mobile.webp') }}" as="image">
    @else
        <link rel="preload" href="{{ asset('frontend-images/optimized-images/hero-mobile.webp') }}" as="image"
            media="(max-width: 768px)">
        <link rel="preload" href="{{ asset('frontend-images/optimized-images/hero-desktop.webp') }}" as="image"
            media="(min-width: 769px)">
    @endif

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">

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
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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

            /* Custom scrollbar for mobile */
            ::-webkit-scrollbar {
                width: 4px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            ::-webkit-scrollbar-thumb {
                background: #c7c7c7;
                border-radius: 2px;
            }

            /* Smooth transitions */
            .transition-all {
                transition-property: all;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 200ms;
            }
        @endif

        /* Playfair Display font */
        .playfair-display {
            font-family: 'Playfair Display', serif;
        }

        /* Line clamp utility */
        .line-clamp-1 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 1;
        }

        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }

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
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white min-h-screen flex flex-col">
    <main class="flex-grow">
        {{-- Navbar --}}
        <x-navbar />

        {{-- Mobile App Content --}}
        @if($isMobileApp)
            <div class="mobile-app-home pb-4">
                {{-- Enhanced App Hero Banner --}}
                <div class="relative w-full h-64 overflow-hidden">
                    <!-- Background Image with Gradient Overlay -->
                    <div class="absolute inset-0">
                        <picture>
                            <source media="(min-width: 768px)"
                                srcset="{{ asset('frontend-images/optimized-images/hero-tablet.webp') }}">
                            <img src="{{ asset('frontend-images/optimized-images/hero-mobile.webp') }}" alt="Fashion Rental"
                                class="absolute inset-0 w-full h-full object-cover"
                                loading="eager" width="768" height="512">
                        </picture>
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-900/50 to-indigo-900/40"></div>
                        <div class="absolute inset-0 bg-black/20"></div>
                    </div>

                    <!-- Content -->
                    <div class="relative z-10 h-full flex flex-col justify-center px-6">
                        <h1 class="text-white text-2xl font-bold mb-2 playfair-display leading-tight">
                            Wear the Moment.<br>
                            <span class="bg-gradient-to-r from-purple-300 to-indigo-300 bg-clip-text text-transparent">
                                Rent with Ease.
                            </span>
                        </h1>
                        <p class="text-gray-200 text-sm mb-6 max-w-xs">
                            Discover premium fashion rentals from trusted vendors across Davao.
                        </p>
                        <div class="flex space-x-3">
                            <a href="/product-list?app=1&mobile_nav=true"
                                class="inline-flex items-center justify-center px-5 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-indigo-700 transform hover:scale-105 transition-all duration-300 group border-0 shadow-lg">
                                <span class="mr-2">Browse</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform duration-200"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </a>
                            <a href="/shops?app=1&mobile_nav=true"
                                class="inline-flex items-center justify-center px-5 py-3 bg-white/10 backdrop-blur-sm text-white font-semibold rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20 shadow-lg">
                                <span>Shops</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Quick Categories --}}
                <div class="px-6 py-6">
                    <div class="flex justify-between items-center mb-5">
                        <h2 class="text-lg font-semibold text-gray-900">Categories</h2>
                        <a href="/product-list?app=1&mobile_nav=true" class="text-sm text-purple-600 font-medium">View all</a>
                    </div>
                    
                    <div class="grid grid-cols-4 gap-3">
                        <a href="/product-list?type=Gown&app=1&mobile_nav=true" 
                           class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 active:scale-95">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 text-center">Gowns</span>
                        </a>
                        
                        <a href="/product-list?type=Suit&app=1&mobile_nav=true" 
                           class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 active:scale-95">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 text-center">Suits</span>
                        </a>
                        
                        <a href="/shops?app=1&mobile_nav=true" 
                           class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 active:scale-95">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-pink-100 to-pink-50 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 text-center">Shops</span>
                        </a>
                        
                        <a href="{{ auth()->check() ? '/account?app=1&mobile_nav=true' : '/login?app=1&mobile_nav=true' }}" 
                           class="flex flex-col items-center p-3 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 active:scale-95">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-yellow-100 to-yellow-50 flex items-center justify-center mb-2">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700 text-center">Favorites</span>
                        </a>
                    </div>
                </div>

                {{-- Featured Products --}}
                <div class="px-6 py-6 bg-gray-50/50">
                    <div class="flex justify-between items-center mb-5">
                        <h2 class="text-lg font-semibold text-gray-900">Featured</h2>
                        <a href="/product-list?app=1&mobile_nav=true" class="text-sm text-purple-600 font-medium">See all</a>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse ($products->take(3) as $product)
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-200 hover:shadow-md active:scale-[0.98]">
                                <a href="{{ route('product.overview', ['product_id' => $product->product_id]) }}?app=1&mobile_nav=true" 
                                   class="flex">
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
                                            <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-1 p-3">
                                        <h3 class="font-medium text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                                        <div class="flex items-center mb-2">
                                            <span class="text-xs px-2 py-1 bg-purple-100 text-purple-700 rounded-full capitalize">
                                                {{ $product->subtype }}
                                            </span>
                                        </div>
                                        <div class="flex items-center text-sm text-purple-600 font-medium">
                                            <span>View details</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m8-8V4a1 1 0 00-1-1h-2a1 1 0 00-1 1v1M9 7h6" />
                                </svg>
                                <p class="text-gray-500 mb-4">No featured products available</p>
                                <a href="/product-list?app=1&mobile_nav=true"
                                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition-all duration-200 active:scale-95">
                                    Browse Collections
                                </a>
                            </div>
                        @endforelse
                    </div>
                    
                    @if($products->count() > 3)
                        <div class="mt-6 text-center">
                            <a href="/product-list?app=1&mobile_nav=true"
                                class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-all duration-200 active:scale-95 shadow-sm">
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
                <div class="px-6 py-8">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 text-center playfair-display">Why Choose DressUp?</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-purple-100 to-purple-50 flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.6l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.6l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Premium Quality</h3>
                                <p class="text-sm text-gray-600 leading-relaxed">Curated collection of designer items from trusted vendors, ensuring excellence and craftsmanship.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Multiple Vendors</h3>
                                <p class="text-sm text-gray-600 leading-relaxed">Access a diverse network of verified rental shops across Davao for more choices and styles.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-gradient-to-br from-pink-100 to-pink-50 flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Easy Experience</h3>
                                <p class="text-sm text-gray-600 leading-relaxed">Simple booking process, flexible rental periods, and dedicated customer support.</p>
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
                
                // Add active state for touch feedback
                document.querySelectorAll('a, button').forEach(element => {
                    element.addEventListener('touchstart', function() {
                        this.classList.add('active:scale-95');
                    });
                    
                    element.addEventListener('touchend', function() {
                        this.classList.remove('active:scale-95');
                    });
                });
            @endif
        });
    </script>
</body>
</html>
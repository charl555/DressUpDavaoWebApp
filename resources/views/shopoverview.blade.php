@php
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $shop->shop_name }} - Shop Overview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    @if($isMobileApp)
        <style>
            /* Mobile-specific styles */
            body {
                padding-top: 64px !important;
                padding-bottom: 80px !important;
                background-color: #fafafa;
                -webkit-tap-highlight-color: transparent;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }

            * {
                -webkit-overflow-scrolling: touch;
            }

            /* Better touch targets */
            button,
            a {
                min-height: 44px;
                min-width: 44px;
            }

            /* Custom scrollbar */
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

            .line-clamp-3 {
                overflow: hidden;
                display: -webkit-box;
                -webkit-box-orient: vertical;
                -webkit-line-clamp: 3;
            }
        </style>
    @endif

    @unless($isMobileApp)
        <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
    @endunless

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#1f2937" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="DressUp" />
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png" />
</head>

<body class="bg-white min-h-screen flex flex-col">
    <main class="flex-grow">
        @if($isMobileApp)
            {{-- Mobile Header --}}
            <div class="fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-200 px-4 py-3 shadow-sm pt-safe">
                <div class="flex items-center justify-between">
                    <button onclick="window.history.back()"
                        class="p-2 rounded-full hover:bg-gray-100 active:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <h1 class="text-lg font-semibold text-gray-900 playfair-display line-clamp-1">Shop Profile</h1>

                    <div class="w-10"></div> {{-- Spacer for alignment --}}
                </div>
            </div>

            {{-- Mobile Shop Details --}}
            <div class="mobile-app-shop pt-16 pb-20">
                <x-shopdetailscardcomponent-mobile :shop="$shop" :reviews="$reviews" :averageRating="$averageRating" />

                {{-- Mobile Products List --}}
                <div class="mt-6 border-t border-gray-200 pt-6">
                    <h2 class="text-xl font-bold text-gray-900 px-4 mb-4">Shop Products</h2>
                    <x-productlist-mobile :products="$products" />
                </div>
            </div>

            {{-- Mobile Bottom Navbar --}}
            <x-bottom-navbar />
        @else
            {{-- Desktop View --}}
            <x-navbar />
            <x-shopdetailscardcomponent :shop="$shop" :reviews="$reviews" :averageRating="$averageRating" />
            <x-productlistcomponent :products="$products" />
        @endif
    </main>

    @unless($isMobileApp)
        <x-footer />
        <x-toast />
    @endunless
    <x-chatwindow />
</body>

</html>
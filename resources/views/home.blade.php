<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Homepage - Dress Up Davao</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap"
        as="style">

    <link rel="preload" href="{{ asset('frontend-images/optimized-images/hero-mobile.webp') }}" as="image"
        media="(max-width: 768px)">
    <link rel="preload" href="{{ asset('frontend-images/optimized-images/hero-desktop.webp') }}" as="image"
        media="(min-width: 769px)">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://js.pusher.com">

    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#1f2937" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="DressUp" />
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png" />

    <style>
        .bg-black {
            background-color: #000;
        }

        .text-white {
            color: #fff;
        }

        .flex {
            display: flex;
        }

        .w-full {
            width: 100%;
        }

        .h-\[500px\] {
            height: 500px;
        }

        .absolute {
            position: absolute;
        }

        .inset-0 {
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
        }

        .justify-center {
            justify-content: center;
        }

        .items-center {
            align-items: center;
        }

        .z-10 {
            z-index: 10;
        }

        /* Skeleton styles */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        .skeleton-text,
        .skeleton-image,
        .skeleton-button {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Mobile app specific styles - applied conditionally via PHP */
        @php
            $isMobileApp = request()->has('app') ||
                request()->has('mobile_nav') ||
                str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
        @endphp

        @if($isMobileApp)
            body {
                padding-top: 64px !important;
                padding-bottom: 64px !important;

            }
        @endif
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap">
</head>

<body class="bg-white min-h-screen flex flex-col">
    <main class="flex-grow">
        {{-- Navbar (contains both web and mobile versions) --}}
<x-navbar />
        
        {{-- Your page content --}}
        <x-hero />
        <x-infocardscomponent />
        <x-category />
<x-productcards :products="$products" />
        
        {{-- Bottom navbar for mobile app --}}
        <x-bottom-navbar />
    </main>

    <x-footer />
    <x-chatwindow />
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

            if ('requestIdleCallback' in window) {
                requestIdleCallback(() => {
                    const preloadLinks = ['/product-list', '/about'];
                    preloadLinks.forEach(link => {
                        const preloadLink = document.createElement('link');
                        preloadLink.rel = 'prefetch';
                        preloadLink.href = link;
                        document.head.appendChild(preloadLink);
                    });
                });
}
            
            // Mobile app specific JavaScript
            @php
                $isMobileApp = request()->has('app') ||
                    request()->has('mobile_nav') ||
                    str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
            @endphp
            
            @if($isMobileApp)
                console.log('Mobile app UI loaded');
                // Add any mobile-specific JavaScript here
            @endif
        });
    </script>
</body>

</html>
@php
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Shop Center</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    @if($isMobileApp)
        <style>
            /* Mobile-specific styles */
            body {
                padding-top: 64px !important;
                padding-bottom: 64px !important;
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
        </style>
    @endif

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
        <x-navbar />

        @if($isMobileApp)
            <x-shopcards-mobile :shops="$shops" :search="$search ?? ''" />
        @else
            <x-shopcards :shops="$shops" :search="$search ?? ''" />
        @endif

        @if($isMobileApp)
            <x-bottom-navbar />
        @else

        @endif
    </main>

    @unless($isMobileApp)
        <x-footer />
    @endunless
    <x-chatwindow />
    <x-toast />

    @if($isMobileApp)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Mobile app specific JavaScript
                console.log('Mobile shops page loaded');

                // Add active state for touch feedback
                document.querySelectorAll('a, button').forEach(element => {
                    element.addEventListener('touchstart', function () {
                        this.classList.add('active:scale-95');
                    });

                    element.addEventListener('touchend', function () {
                        this.classList.remove('active:scale-95');
                    });
                });

                // Pull-to-refresh prevention for app
                let touchStartY = 0;
                document.addEventListener('touchstart', (e) => {
                    touchStartY = e.touches[0].clientY;
                }, { passive: true });

                document.addEventListener('touchmove', (e) => {
                    const touchY = e.touches[0].clientY;
                    const diff = touchY - touchStartY;

                    // Prevent pull-to-refresh when not at top
                    if (window.scrollY > 0 && diff > 50) {
                        e.preventDefault();
                    }
                }, { passive: false });
            });
        </script>
    @endif
</body>

</html>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Account Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="manifest" href="/manifest.json" />
    <meta name="theme-color" content="#1f2937" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="apple-mobile-web-app-title" content="DressUp" />
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png" />
    
    <style>
        @php
            $isMobileApp = request()->has('app') ||
                request()->has('mobile_nav') ||
                str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
        @endphp
        
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
    </style>
</head>

<body class="bg-white min-h-screen flex flex-col">
    <main class="flex-grow">
        <x-navbar />
        <x-accountsettings :bookings="$bookings" :favorites="$favorites" />
        <x-bottom-navbar />
        
        @php
            $isMobileApp = request()->has('app') ||
                request()->has('mobile_nav') ||
                str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
        @endphp
        
        {{-- Show chatwindow only on desktop, mobile uses bottom navbar chat --}}
        @unless($isMobileApp)
            <x-chatwindow />
        @endunless
    </main>
    
    @unless($isMobileApp)
        <x-footer />
    @endunless
    
    <x-toast />

    <script>
        @if($isMobileApp)
            // Mobile app specific JavaScript
            document.addEventListener('DOMContentLoaded', function () {
                console.log('Mobile account page loaded');
                
                // Add active state for touch feedback
                document.querySelectorAll('a, button').forEach(element => {
                    element.addEventListener('touchstart', function() {
                        this.classList.add('active:scale-95');
                    });
                    
                    element.addEventListener('touchend', function() {
                        this.classList.remove('active:scale-95');
                    });
                });
            });
        @endif
    </script>
</body>

</html>
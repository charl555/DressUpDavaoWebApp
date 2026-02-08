@php
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - DressUp Davao</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if($isMobileApp)
        <style>
            /* Mobile-specific styles */
            body {
                padding-top: 0 !important;
                padding-bottom: 0 !important;
                background-color: #fafafa;
                -webkit-tap-highlight-color: transparent;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }

            * {
                -webkit-overflow-scrolling: touch;
            }

            /* Better touch targets */
            button,
            a,
            input {
                min-height: 44px;
            }

            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 4px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            ::webkit-scrollbar-thumb {
                background: #c7c7c7;
                border-radius: 2px;
            }

            /* Smooth transitions */
            .transition-all {
                transition-property: all;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 200ms;
            }

            /* Hide scrollbar for Chrome, Safari and Opera */
            .no-scrollbar::-webkit-scrollbar {
                display: none;
            }

            /* Hide scrollbar for IE, Edge and Firefox */
            .no-scrollbar {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            /* Mobile-safe text sizes */
            .text-base-mobile {
                font-size: 16px !important;
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

<body class="bg-white h-full">
    <main>
        @if($isMobileApp)
            <x-register-mobile />
        @else
            <x-registerform />
        @endif
    </main>
    <x-toast />

    @if(!$isMobileApp)
        <!-- Cloudflare Turnstile Script (Desktop only) -->
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
</body>

</html>
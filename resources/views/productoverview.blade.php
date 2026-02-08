<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Product Overview</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
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
            
            /* Ensure inquire modal works properly on mobile */
            .fixed.inset-0 {
                z-index: 9999 !important;
            }
        @endif
    </style>
</head>

<body class="bg-white min-h-screen flex flex-col">
    <main class="flex-grow">
        <x-navbar />
        
        {{-- Conditionally apply padding-top based on device type --}}
        <div class="@if($isMobileApp) pt-0 @else pt-[100px] @endif">
            <x-overview :product="$product" />
        </div>
        
        <x-bottom-navbar />
        
        @php
            $isMobileApp = request()->has('app') ||
                request()->has('mobile_nav') ||
                str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
        @endphp
        
        {{-- Show chatwindow only on desktop, mobile uses bottom navbar chat --}}
        @unless($isMobileApp)
            
        @endunless
        
        {{-- Inquire modal should be available on both mobile and desktop --}}
        <x-inquire />
    </main>
    
    @unless($isMobileApp)
        <x-footer />
    @endunless
    
    <x-chatwindow />
    <x-toast />

    <script>
        @if($isMobileApp)
            // Mobile app specific JavaScript
            document.addEventListener('DOMContentLoaded', function () {
                console.log('Mobile product overview loaded');
                
                // Add active state for touch feedback
                document.querySelectorAll('a, button').forEach(element => {
                    element.addEventListener('touchstart', function() {
                        this.classList.add('active:scale-95');
                    });
                    
                    element.addEventListener('touchend', function() {
                        this.classList.remove('active:scale-95');
                    });
                });
                
                // Enhance mobile UX for product images
                const productImages = document.querySelectorAll('.product-image');
                productImages.forEach(img => {
                    img.addEventListener('click', function() {
                        this.classList.toggle('scale-105');
                    });
                });
                
                // Additional mobile optimization: Adjust content spacing
                const mainContent = document.querySelector('main');
                if (mainContent) {
                    // Remove any additional top padding that might interfere with fixed navbar
                    mainContent.style.paddingTop = '0';
                }
            });
        @endif
        
        // Optional: Dynamically adjust spacing on window resize
        window.addEventListener('resize', function() {
            @if(!$isMobileApp)
                // On desktop, ensure proper spacing
                const contentDiv = document.querySelector('.pt-\\[100px\\]');
                if (contentDiv && window.innerWidth >= 768) {
                    contentDiv.classList.remove('pt-0');
                    contentDiv.classList.add('pt-[100px]');
                }
            @endif
        });
    </script>
</body>

</html>
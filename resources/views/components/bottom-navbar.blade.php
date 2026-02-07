{{-- resources/views/components/bottom-navbar.blade.php --}}
@php
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
@endphp

@if($isMobileApp)
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t border-gray-200 shadow-lg">
        <div class="flex justify-around items-center h-16">
            {{-- Home --}}
            <a href="/?app=1&mobile_nav=true"
                class="flex flex-col items-center justify-center w-full h-full {{ request()->is('/') ? 'text-purple-600' : 'text-gray-500' }} transition-colors duration-200 hover:bg-purple-50">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-xs font-medium">Home</span>
            </a>

            {{-- Collections --}}
            <a href="/product-list?app=1&mobile_nav=true"
                class="flex flex-col items-center justify-center w-full h-full {{ request()->is('product-list*') ? 'text-purple-600' : 'text-gray-500' }} transition-colors duration-200 hover:bg-purple-50">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span class="text-xs font-medium">Collections</span>
            </a>

            {{-- Shops --}}
            <a href="/shops?app=1&mobile_nav=true"
                class="flex flex-col items-center justify-center w-full h-full {{ request()->is('shops*') ? 'text-purple-600' : 'text-gray-500' }} transition-colors duration-200 hover:bg-purple-50">
                <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-xs font-medium">Shops</span>
            </a>

            {{-- Profile/Login --}}
            @auth
                @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin')
                    <a href="/account?app=1&mobile_nav=true"
                        class="flex flex-col items-center justify-center w-full h-full {{ request()->is('account*') ? 'text-purple-600' : 'text-gray-500' }} transition-colors duration-200 hover:bg-purple-50">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-xs font-medium">Profile</span>
                    </a>
                @else
                    {{-- For admins, show dashboard instead --}}
                    <a href="/admin?app=1&mobile_nav=true"
                        class="flex flex-col items-center justify-center w-full h-full {{ request()->is('admin*') ? 'text-purple-600' : 'text-gray-500' }} transition-colors duration-200 hover:bg-purple-50">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="text-xs font-medium">Admin</span>
                    </a>
                @endif
            @else
                <a href="/login?app=1&mobile_nav=true"
                    class="flex flex-col items-center justify-center w-full h-full {{ request()->is('login*') ? 'text-purple-600' : 'text-gray-500' }} transition-colors duration-200 hover:bg-purple-50">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    <span class="text-xs font-medium">Login</span>
                </a>
            @endauth
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle active states for bottom nav
            const currentPath = window.location.pathname;
            const bottomNavLinks = document.querySelectorAll('nav a');

            bottomNavLinks.forEach(link => {
                try {
                    const linkUrl = new URL(link.href);
                    const linkPath = linkUrl.pathname;

                    // Check if current path matches link path
                    if (currentPath === linkPath ||
                        (currentPath.includes('/product-list') && linkPath.includes('/product-list')) ||
                        (currentPath.includes('/shops') && linkPath.includes('/shops')) ||
                        (currentPath.includes('/account') && linkPath.includes('/account')) ||
                        (currentPath.includes('/login') && linkPath.includes('/login')) ||
                        (currentPath.includes('/admin') && linkPath.includes('/admin'))) {

                        // Remove existing classes
                        link.classList.remove('text-gray-500');
                        link.classList.add('text-purple-600');

                        // Add active indicator
                        const indicator = document.createElement('div');
                        indicator.className = 'absolute top-0 left-1/2 transform -translate-x-1/2 w-1 h-1 bg-purple-600 rounded-full';
                        link.appendChild(indicator);
                    }
                } catch (e) {
                    console.log('Error processing link:', e);
                }
            });
        });
    </script>

    <style>
        /* Add padding to body for fixed bottom nav */
        body {
            padding-bottom: 64px !important;
        }

        /* Active state indicator */
        nav a {
            position: relative;
        }

        /* Touch-friendly tap targets */
        nav a {
            -webkit-tap-highlight-color: transparent;
            user-select: none;
        }

        /* Hover effect for non-touch devices */
        @media (hover: hover) {
            nav a:hover {
                background-color: rgba(126, 34, 206, 0.05);
            }
        }

        /* Mobile-specific styles */
        @media (max-width: 768px) {
            nav a {
                padding: 8px 0;
            }

            nav a svg {
                width: 22px;
                height: 22px;
            }

            nav a span {
                font-size: 10px;
            }
        }
    </style>
@endif
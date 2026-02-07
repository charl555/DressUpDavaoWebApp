{{-- resources/views/components/navbar.blade.php --}}
@php
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
@endphp

{{-- WEB NAVBAR (shown only on regular web) --}}
@unless($isMobileApp)
    <div id="navbar"
        class="navbar-container fixed top-0 left-0 w-full z-50 transition-transform duration-300 ease-in-out bg-white text-black shadow-sm border-b border-gray-100"
        x-data="{ openUser: false }">

        {{-- Single Row Layout --}}
        <div class="flex items-center justify-between px-4 py-3 sm:px-6 lg:px-10">
            {{-- Logo on the left --}}
            <div class="flex items-center flex-1 justify-start">
                <a href="/" class="flex items-center space-x-3">
                    <img src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao" class="h-12 w-auto" />
                </a>
            </div>

            {{-- Navigation links in the middle --}}
            <div class="hidden lg:flex items-center flex-1 justify-center">
                <ol class="flex items-center gap-8">
                    <li>
                        <a href="/product-list"
                            class="nav-link-fade font-medium text-gray-700 hover:text-white transition-all duration-300 ease-out relative overflow-hidden px-4 py-2 rounded-lg">
                            Collections
                        </a>
                    </li>
                    <li>
                        <a href="/shops"
                            class="nav-link-fade font-medium text-gray-700 hover:text-white transition-all duration-300 ease-out relative overflow-hidden px-4 py-2 rounded-lg">
                            Shops
                        </a>
                    </li>
                    @auth
                        {{-- Hide bookings link for admins --}}
                        @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin')
                            <li>
                                <a href="/account#my-bookings"
                                    class="nav-link-fade font-medium text-gray-700 hover:text-white transition-all duration-300 ease-out relative overflow-hidden px-4 py-2 rounded-lg">
                                    Bookings
                                </a>
                            </li>
                        @endif
                    @endauth
                    @guest
                        <li>
                            <a href="/login"
                                class="nav-link-fade font-medium text-gray-700 hover:text-white transition-all duration-300 ease-out relative overflow-hidden px-4 py-2 rounded-lg">
                                Bookings
                            </a>
                        </li>
                        <li>
                            <a href="/login"
                                class="nav-link-fade font-medium text-gray-700 hover:text-white transition-all duration-300 ease-out relative overflow-hidden px-4 py-2 rounded-lg">
                                Favorites
                            </a>
                        </li>
                    @endguest
                    @auth
                        @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin')
                            <li>
                                <a href="/account#my-favorites"
                                    class="nav-link-fade font-medium text-gray-700 hover:text-white transition-all duration-300 ease-out relative overflow-hidden px-4 py-2 rounded-lg">
                                    Favorites
                                </a>
                            </li>
                        @endif
                    @endauth
                    @if(auth()->guest() || (auth()->check() && (auth()->user()->role === 'Admin' || auth()->user()->role === 'SuperAdmin')))
                        <li>
                            <a href="/shop-center"
                                class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg text-sm whitespace-nowrap">
                                Register your Shop
                            </a>
                        </li>
                    @endif
                </ol>
            </div>

            {{-- User menu on the right --}}
            <div class="flex items-center space-x-4 flex-1 justify-end">
                {{-- Desktop User Menu - Visible links instead of dropdown --}}
                @auth
                    {{-- Hide user menu for admins --}}
                    @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin')
                        <div class="hidden lg:flex items-center space-x-6">
                            <a href="/account"
                                class="nav-link-fade font-medium text-gray-700 hover:text-white transition-all duration-300 ease-out relative overflow-hidden px-4 py-2 rounded-lg flex items-center space-x-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Account</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="m-0">
                                @csrf
                                <button type="submit"
                                    class="nav-link-fade font-medium text-gray-700 hover:text-white transition-all duration-300 ease-out relative overflow-hidden px-4 py-2 rounded-lg flex items-center space-x-1">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth

                @guest
                    <div class="hidden lg:flex items-center space-x-4">
                        <a href="/login"
                            class="px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-md hover:shadow-lg">
                            Sign in
                        </a>
                    </div>
                @endguest

                <!-- Mobile Menu Button -->
                <div class="lg:hidden">
                    <button id="mobile-menu-button"
                        class="text-gray-700 focus:outline-none hover:text-purple-700 transition-colors duration-200 p-2 rounded-lg hover:bg-gray-50">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Admin Banner - INSIDE the navbar container --}}
        @auth
            @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'SuperAdmin')
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200 py-2 px-4 h-10 flex items-center">
                    <div class="max-w-screen-xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 w-full">
                        <div class="flex items-center space-x-2">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-blue-800 font-medium text-sm">You are viewing as a customer</span>
                            <span class="text-blue-600 text-xs">(Shop Owner Mode)</span>
                        </div>
                        <a href="/admin"
                            class="inline-flex items-center px-4 py-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-medium rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 shadow-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Admin Dashboard
                        </a>
                    </div>
                </div>
            @endif
        @endauth

        <!-- Mobile Menu -->
        <div id="mobile-menu"
            class="lg:hidden absolute top-full left-0 w-full bg-white shadow-lg border-b border-gray-100 py-0 overflow-hidden max-h-0 opacity-0 transition-all duration-300 ease-in-out z-50">
            <ol class="flex flex-col items-stretch">
                <li>
                    <a href="/product-list"
                        class="block px-6 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200 border-b border-gray-100 font-medium">
                        Collections
                    </a>
                </li>
                <li>
                    <a href="/shops"
                        class="block px-6 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200 border-b border-gray-100 font-medium">
                        Shops
                    </a>
                </li>

                @guest
                    <li>
                        <a href="/login"
                            class="block px-6 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200 border-b border-gray-100 font-medium">
                            Bookings
                        </a>
                    </li>
                    <li>
                        <a href="/login"
                            class="block px-6 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200 border-b border-gray-100 font-medium">
                            Favorites
                        </a>
                    </li>
                @endguest

                @auth
                    {{-- Show bookings and favorites for authenticated non-admin users --}}
                    @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin')
                        <li>
                            <a href="/account#my-bookings"
                                class="block px-6 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200 border-b border-gray-100 font-medium">
                                Bookings
                            </a>
                        </li>
                        <li>
                            <a href="/account#my-favorites"
                                class="block px-6 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200 border-b border-gray-100 font-medium">
                                Favorites
                            </a>
                        </li>
                    @endif
                @endauth

                @if(auth()->guest() || (auth()->check() && (auth()->user()->role === 'Admin' || auth()->user()->role === 'SuperAdmin')))
                    <li class="border-b border-gray-100">
                        <a href="/shop-center"
                            class="block mx-4 my-3 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 rounded-lg text-center text-sm">
                            Register your Shop
                        </a>
                    </li>
                @endif

                @auth
                    {{-- Hide user menu items for admins --}}
                    @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin')
                        <li class="border-t border-gray-200 mt-2"></li>
                        <li>
                            <a href="/account"
                                class="block px-6 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200 border-b border-gray-100 font-medium flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>Account Settings</span>
                            </a>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="block w-full text-left px-6 py-3 text-gray-700 hover:bg-purple-50 hover:text-purple-700 transition-colors duration-200 font-medium flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Log Out</span>
                                </button>
                            </form>
                        </li>
                    @endif
                @endauth

                @guest
                    <li class="border-t border-gray-200 mt-2">
                        <a href="/login"
                            class="block mx-4 my-3 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 rounded-lg text-center text-sm">
                            Sign in
                        </a>
                    </li>
                @endguest
            </ol>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuButton && mobileMenu) {
                mobileMenuButton.addEventListener('click', function () {
                    if (mobileMenu.classList.contains('opacity-0')) {
                        mobileMenu.classList.remove('opacity-0', 'max-h-0');
                        mobileMenu.classList.add('opacity-100', 'max-h-screen', 'py-0');
                    } else {
                        mobileMenu.classList.remove('opacity-100', 'max-h-screen', 'py-0');
                        mobileMenu.classList.add('opacity-0', 'max-h-0');
                    }
                });

                document.addEventListener('click', function (event) {
                    if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target)) {
                        if (mobileMenu.classList.contains('opacity-100')) {
                            mobileMenu.classList.remove('opacity-100', 'max-h-screen', 'py-0');
                            mobileMenu.classList.add('opacity-0', 'max-h-0');
                        }
                    }
                });
            }
        });

        document.addEventListener("DOMContentLoaded", function () {
            let lastScrollTop = 0;
            const navbar = document.getElementById('navbar');
            const scrollThreshold = 150;

            if (navbar) {
                window.addEventListener('scroll', function () {
                    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
                    if (currentScroll > scrollThreshold) {
                        if (currentScroll > lastScrollTop) navbar.classList.add('navbar-hidden');
                        else navbar.classList.remove('navbar-hidden');
                    } else {
                        navbar.classList.remove('navbar-hidden');
                    }
                    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
                });
            }
        });
    </script>

    <style>
        .nav-link-fade {
            position: relative;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease-out;
            z-index: 1;
            background: transparent;
        }

        .nav-link-fade::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, #7c3aed, #4f46e5);
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.3s ease-out;
            z-index: -1;
        }

        .nav-link-fade:hover::before {
            opacity: 1;
        }

        .nav-link-fade:hover {
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        }

        .hover-underline-animation {
            position: relative;
            text-decoration: none;
            padding-bottom: 2px;
        }

        .hover-underline-animation::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: 0;
            left: 0;
            background: linear-gradient(to right, #7c3aed, #4f46e5);
            transform: scaleX(0);
            transform-origin: bottom left;
            transition: transform 0.3s ease-in-out;
        }

        .hover-underline-animation:hover::after {
            transform: scaleX(1);
        }

        .navbar-container {
            transform: translateY(0);
        }

        .navbar-hidden {
            transform: translateY(-100%);
        }
    </style>
@endunless

{{-- MOBILE APP NAVBAR (shown only in Android app) --}}
@if($isMobileApp)
    <div id="mobile-app-navbar" class="fixed top-0 left-0 right-0 z-50 bg-white shadow-lg border-b border-gray-200">
        <div class="h-16 flex items-center justify-between px-4">
            {{-- Left: Logo/Title --}}
            <div class="flex items-center">
                <a href="/?app=1&mobile_nav=true" class="flex items-center space-x-2">
                    <img src="{{ asset('images/Dressupdavaologo.png') }}" alt="DressUp Davao" class="h-8 w-auto" />
                    <span class="text-lg font-bold text-purple-600 hidden sm:inline">DressUp Davao</span>
                </a>
            </div>

            {{-- Right: Mobile Menu Button --}}
            <button id="mobile-app-menu-button"
                class="p-2 rounded-lg text-purple-600 hover:bg-purple-50 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Mobile Dropdown Menu --}}
        <div id="mobile-app-menu"
            class="absolute top-full left-0 right-0 bg-white shadow-lg border-b border-gray-200 max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
            <div class="py-2 px-4">

                {{-- Account Section --}}
                @auth
                    @if(auth()->user()->role !== 'Admin' && auth()->user()->role !== 'SuperAdmin')
                        <div class="mb-3 pb-3 border-b border-gray-100">
                            <p class="text-xs text-gray-500 mb-1">Logged in as</p>
                            <p class="font-medium text-gray-800">{{ auth()->user()->name }}</p>
                        </div>

                        <a href="/account?app=1&mobile_nav=true"
                            class="flex items-center space-x-3 px-3 py-3 rounded-lg hover:bg-purple-50 text-gray-700 transition-colors duration-200">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="font-medium">My Account</span>
                        </a>

                        <a href="/account#my-bookings?app=1&mobile_nav=true"
                            class="flex items-center space-x-3 px-3 py-3 rounded-lg hover:bg-purple-50 text-gray-700 transition-colors duration-200">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span class="font-medium">My Bookings</span>
                        </a>

                        <a href="/account#my-favorites?app=1&mobile_nav=true"
                            class="flex items-center space-x-3 px-3 py-3 rounded-lg hover:bg-purple-50 text-gray-700 transition-colors duration-200">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            <span class="font-medium">Favorites</span>
                        </a>

                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <input type="hidden" name="app" value="1">
                            <input type="hidden" name="mobile_nav" value="true">
                            <button type="submit"
                                class="flex items-center space-x-3 w-full px-3 py-3 rounded-lg hover:bg-red-50 text-red-600 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span class="font-medium">Log Out</span>
                            </button>
                        </form>
                    @endif
                @endauth

                @guest
                    <a href="/login?app=1&mobile_nav=true"
                        class="flex items-center space-x-3 px-3 py-3 rounded-lg hover:bg-purple-50 text-gray-700 transition-colors duration-200 mb-3">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span class="font-medium">Sign In</span>
                    </a>
                @endguest

                {{-- Shop Registration --}}
                @if(auth()->guest() || (auth()->check() && (auth()->user()->role === 'Admin' || auth()->user()->role === 'SuperAdmin')))
                    <a href="/shop-center?app=1&mobile_nav=true"
                        class="mt-3 flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        Register your Shop
                    </a>
                @endif

                {{-- Admin Link --}}
                @auth
                    @if(auth()->user()->role === 'Admin' || auth()->user()->role === 'SuperAdmin')
                        <div class="mt-4 pt-3 border-t border-gray-200">
                            <a href="/admin?app=1&mobile_nav=true"
                                class="flex items-center space-x-3 px-3 py-3 rounded-lg hover:bg-blue-50 text-blue-600 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="font-medium">Admin Dashboard</span>
                            </a>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuButton = document.getElementById('mobile-app-menu-button');
            const mobileMenu = document.getElementById('mobile-app-menu');

            if (menuButton && mobileMenu) {
                menuButton.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isOpen = mobileMenu.classList.contains('max-h-0');

                    if (isOpen) {
                        mobileMenu.classList.remove('max-h-0');
                        mobileMenu.classList.add('max-h-96', 'py-2', 'border-b');
                    } else {
                        mobileMenu.classList.remove('max-h-96', 'py-2', 'border-b');
                        mobileMenu.classList.add('max-h-0');
                    }
                });

                document.addEventListener('click', function (e) {
                    if (mobileMenu && menuButton && !mobileMenu.contains(e.target) && !menuButton.contains(e.target)) {
                        mobileMenu.classList.remove('max-h-96', 'py-2', 'border-b');
                        mobileMenu.classList.add('max-h-0');
                    }
                });

                if (mobileMenu.querySelectorAll('a, button').length > 0) {
                    mobileMenu.querySelectorAll('a, button').forEach(element => {
                        element.addEventListener('click', function () {
                            mobileMenu.classList.remove('max-h-96', 'py-2', 'border-b');
                            mobileMenu.classList.add('max-h-0');
                        });
                    });
                }
            }
        });
    </script>
@endif

{{-- Add body padding for mobile app navbar --}}
@if($isMobileApp)
    <style>
        body {
            padding-top: 64px !important;
        }
    </style>
@endif
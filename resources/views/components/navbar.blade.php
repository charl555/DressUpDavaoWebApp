@php
    $isMobileApp = request()->has('app') ||
        request()->has('mobile_nav') ||
        str_contains(request()->header('User-Agent'), 'DressUpDavaoApp');
@endphp

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
@endunless

<!-- Alpine.js is loaded globally via app.js -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

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
    });

    document.addEventListener("DOMContentLoaded", function () {
        let lastScrollTop = 0;
        const navbar = document.getElementById('navbar');
        const scrollThreshold = 150;

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

    /* Alternative version with scale animation */
    .nav-link-scale {
        position: relative;
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.3s ease-out;
        z-index: 1;
        background: transparent;
    }

    .nav-link-scale::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to right, #7c3aed, #4f46e5);
        border-radius: 8px;
        transform: scale(0.8);
        opacity: 0;
        transition: all 0.3s ease-out;
        z-index: -1;
    }

    .nav-link-scale:hover::before {
        opacity: 1;
        transform: scale(1);
    }

    .nav-link-scale:hover {
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
    }

    /* Keep the original animations for reference */
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